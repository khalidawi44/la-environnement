#!/usr/bin/env bash
# ============================================================================
# SessionStart — reprise automatique entre conversations Claude
# ----------------------------------------------------------------------------
# La sortie de ce hook est injectée dans le contexte de Claude au démarrage de
# CHAQUE session (web, PC ou mobile). Plus besoin de réexpliquer où on en est.
#
# Lecture seule, rapide, idempotent, non interactif.
# ============================================================================
set -euo pipefail

cd "${CLAUDE_PROJECT_DIR:-.}"

# --- (Ré)installe les hooks git dans ce conteneur neuf -----------------------
# .git/hooks ne survit pas au conteneur éphémère : on réinstalle à chaque
# session pour que le php -l pré-commit et le tampon HANDOFF restent actifs.
if [ -f scripts/install-git-hooks.sh ]; then
	bash scripts/install-git-hooks.sh >/dev/null 2>&1 || true
fi
if [ -f scripts/stamp-handoff.sh ]; then
	bash scripts/stamp-handoff.sh >/dev/null 2>&1 || true
fi

echo "===== REPRISE DE SESSION — L.A Environnement ====="
echo "(Généré par .claude/hooks/session-start.sh — état réel du dépôt)"
echo

# --- Rappel de production ----------------------------------------------------
echo "## ⚠️ main = PRODUCTION"
echo "Un push sur main est en ligne sur le site du client en moins de 5 minutes"
echo "(cron LAE_GitHub_Sync). Un commit resté sur une branche n'est PAS en ligne."
echo "Toujours php -l avant de committer."
echo

# --- Branche & alignement git -------------------------------------------------
BRANCH="$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo '?')"
echo "## Branche de travail : ${BRANCH}"
echo "Sur Claude Code web, le conteneur est neuf à chaque session."
echo "Seul ce qui est COMMITÉ + POUSSÉ survit. Pense à pousser avant de fermer."
echo

# --- Derniers commits ---------------------------------------------------------
echo "## Derniers commits"
git log --oneline -8 2>/dev/null || echo "(pas d'historique)"
echo

# --- Travail non commité (= ce qui serait perdu) ------------------------------
DIRTY="$(git status --porcelain 2>/dev/null || true)"
if [ -n "$DIRTY" ]; then
	echo "## ⚠️ Modifications NON commitées (à sauvegarder avant fermeture)"
	echo "$DIRTY"
else
	echo "## Arbre de travail propre (rien en attente)"
fi
echo

# --- Focus actif --------------------------------------------------------------
if [ -f .WORKING_ON.md ]; then
	echo "## Chantier en cours (.WORKING_ON.md)"
	sed -n '1,8p' .WORKING_ON.md 2>/dev/null || true
	echo
fi

# --- Reprise rapide depuis HANDOFF.md ----------------------------------------
if [ -f HANDOFF.md ]; then
	echo "## Reprise rapide (en-tête HANDOFF.md)"
	sed -n '1,6p' HANDOFF.md
	echo
	echo "→ Détail complet : lire HANDOFF.md, puis BACKLOG.md."
fi
echo

echo "===== FIN REPRISE — mets à jour HANDOFF.md avant de fermer la session ====="
