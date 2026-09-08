#!/usr/bin/env bash
# ============================================================================
# Installe le pre-commit hook du projet.
# ----------------------------------------------------------------------------
# Le hook fait deux choses :
#   1. php -l sur chaque fichier PHP indexé + validation des JSON indexés.
#      main = production (en ligne sous 5 min) : un PHP cassé poussé casse le
#      site du client. C'est le seul vrai filet, il doit être bloquant.
#   2. Tamponne HANDOFF.md (date + branche) à chaque commit, pour que la
#      reprise entre conversations ne puisse pas se périmer.
#
# Usage : bash scripts/install-git-hooks.sh
# ============================================================================
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
HOOK_PATH="${REPO_ROOT}/.git/hooks/pre-commit"

if [[ ! -d "${REPO_ROOT}/.git" ]]; then
	echo "ERREUR : ${REPO_ROOT} n'est pas un dépôt git." >&2
	exit 1
fi

cat > "${HOOK_PATH}" <<'HOOK'
#!/usr/bin/env bash
# pre-commit — installé par scripts/install-git-hooks.sh. Ne pas éditer ici :
# éditer scripts/install-git-hooks.sh puis relancer le script.
set -euo pipefail

REPO_ROOT="$(git rev-parse --show-toplevel)"
cd "${REPO_ROOT}"

FAIL=0

# --- 1. php -l sur les fichiers PHP indexés ---------------------------------
if command -v php >/dev/null 2>&1; then
	while IFS= read -r f; do
		[ -f "$f" ] || continue
		if ! php -l "$f" >/dev/null 2>&1; then
			echo "PHP CASSÉ : $f" >&2
			php -l "$f" >&2 || true
			FAIL=1
		fi
	done < <(git diff --cached --name-only --diff-filter=ACM -z | tr '\0' '\n' | grep -E '\.php$' || true)
else
	echo "pre-commit : php introuvable, contrôle de syntaxe PHP ignoré." >&2
	echo "             (main = production : vérifie manuellement avant de pousser)" >&2
fi

# --- 2. JSON indexés valides ------------------------------------------------
if command -v python3 >/dev/null 2>&1; then
	while IFS= read -r f; do
		[ -f "$f" ] || continue
		if ! python3 -c "import json,sys;json.load(open(sys.argv[1],encoding='utf-8'))" "$f" >/dev/null 2>&1; then
			echo "JSON INVALIDE : $f" >&2
			FAIL=1
		fi
	done < <(git diff --cached --name-only --diff-filter=ACM -z | tr '\0' '\n' | grep -E '\.json$' || true)
fi

if [ "$FAIL" -ne 0 ]; then
	echo "" >&2
	echo "Commit refusé : corrige les erreurs ci-dessus." >&2
	echo "main est déployé automatiquement en production sous 5 minutes." >&2
	exit 1
fi

# --- 3. Tampon de reprise ----------------------------------------------------
if [[ -f scripts/stamp-handoff.sh ]]; then
	bash scripts/stamp-handoff.sh || true
	git add HANDOFF.md 2>/dev/null || true
fi
HOOK

chmod +x "${HOOK_PATH}"

echo "Installé : ${HOOK_PATH}"
echo "Le pre-commit refuse tout PHP cassé ou JSON invalide, et tamponne HANDOFF.md."
