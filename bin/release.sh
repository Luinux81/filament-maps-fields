#!/bin/bash

# Script de release automatizado para filament-maps-fields
# Uso: ./bin/release.sh [major|minor|patch]

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  Filament Maps Fields - Release Tool  ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""

if [ $# -eq 0 ]; then
    echo -e "${RED}❌ Error: Especifica el tipo de release [major|minor|patch]${NC}"
    echo "Uso: $0 [major|minor|patch]"
    exit 1
fi

RELEASE_TYPE=$1

CURRENT_BRANCH=$(git branch --show-current)
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo -e "${RED}❌ Error: Debes estar en la rama 'main'${NC}"
    echo -e "  Rama actual: ${YELLOW}${CURRENT_BRANCH}${NC}"
    exit 1
fi

if [ -n "$(git status --porcelain)" ]; then
    echo -e "${RED}❌ Error: Hay cambios sin commitear${NC}"
    git status --short
    exit 1
fi

echo -e "${YELLOW}📥 Pulling latest changes...${NC}"
git pull origin main

LAST_TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "v0.0.0")
echo -e "${BLUE}📌 Última versión: ${YELLOW}${LAST_TAG}${NC}"

VERSION=${LAST_TAG#v}
MAJOR=$(echo $VERSION | cut -d. -f1)
MINOR=$(echo $VERSION | cut -d. -f2)
PATCH=$(echo $VERSION | cut -d. -f3)

case $RELEASE_TYPE in
    major) MAJOR=$((MAJOR + 1)); MINOR=0; PATCH=0 ;;
    minor) MINOR=$((MINOR + 1)); PATCH=0 ;;
    patch) PATCH=$((PATCH + 1)) ;;
    *)
        echo -e "${RED}❌ Tipo inválido. Usa: major, minor o patch${NC}"
        exit 1
        ;;
esac

NEW_VERSION="$MAJOR.$MINOR.$PATCH"
NEW_TAG="v$NEW_VERSION"

echo ""
echo -e "${GREEN}🎯 Nueva versión: ${YELLOW}${NEW_TAG}${NC}"
echo ""

read -p "¿Continuar con el release ${NEW_TAG}? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}⚠️  Release cancelado${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}🧪 Ejecutando tests...${NC}"
if composer test > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Tests passed${NC}"
else
    echo -e "${RED}❌ Tests failed${NC}"
    exit 1
fi

echo -e "${YELLOW}🏷️  Creando tag ${NEW_TAG}...${NC}"
git tag -a "$NEW_TAG" -m "Release $NEW_TAG"

echo -e "${YELLOW}📤 Pusheando tag a GitHub...${NC}"
git push origin "$NEW_TAG"

echo ""
echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     ✅ Release ${NEW_TAG} Completado      ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📦 Packagist se actualizará automáticamente${NC}"
echo -e "${BLUE}🔗 GitHub Actions creará el release${NC}"
echo ""
echo -e "${YELLOW}Ver en:${NC}"
echo -e "  • https://github.com/Luinux81/filament-maps-fields/releases"
echo -e "  • https://packagist.org/packages/lbcdev/filament-maps-fields"
echo ""