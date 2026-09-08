#!/usr/bin/env bash
# ============================================================================
# stamp-handoff.sh — tamponne automatiquement l'en-tête de HANDOFF.md
# ----------------------------------------------------------------------------
# Réécrit la ligne « Dernière mise à jour : <date> — branche de travail : … »
# avec la vraie date du jour et la vraie branche courante.
#
# Appelé par le pre-commit git (scripts/install-git-hooks.sh) => l'en-tête de
# reprise ne peut plus mentir, même si personne ne pense à la mettre à jour.
#
# Idempotent : si la ligne est déjà à jour, ne change rien.
# ============================================================================
set -euo pipefail

REPO_ROOT="$(git rev-parse --show-toplevel 2>/dev/null || echo .)"
HANDOFF="${REPO_ROOT}/HANDOFF.md"

[ -f "$HANDOFF" ] || exit 0

BRANCH="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '?')"
TODAY="$(date +%F)"
NEW_LINE="> Dernière mise à jour : ${TODAY} — branche de travail : \`${BRANCH}\` (tampon auto à chaque commit)."

tmp="$(mktemp)"
awk -v repl="$NEW_LINE" '
	!done && /^> Dernière mise à jour :/ { print repl; done=1; next }
	{ print }
' "$HANDOFF" > "$tmp"

if ! cmp -s "$tmp" "$HANDOFF"; then
	mv "$tmp" "$HANDOFF"
else
	rm -f "$tmp"
fi
