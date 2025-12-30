#!/bin/bash
# Vérifier que tous les fichiers nécessaires au déploiement existent

echo "🔍 Vérification des fichiers de déploiement..."

files=(
    "Dockerfile"
    "start.sh"
    "composer.json"
    "composer.lock"
    ".dockerignore"
    "public/index.php"
)

missing=()

for file in "${files[@]}"; do
    if [ ! -f "$file" ]; then
        missing+=("$file")
        echo "❌ Manquant: $file"
    else
        echo "✅ Présent: $file"
    fi
done

if [ ${#missing[@]} -gt 0 ]; then
    echo ""
    echo "⚠️  Fichiers manquants:"
    printf '%s\n' "${missing[@]}"
    exit 1
else
    echo ""
    echo "✅ Tous les fichiers sont présents!"
    exit 0
fi
