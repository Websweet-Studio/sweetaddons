#!/usr/bin/env bash
#
# Lint semua berkas PHP plugin Sweet Addons.
#
# Dipakai oleh .github/workflows/auto-release.yml (job verify) sebelum rilis dibuat,
# dan bisa dijalankan lokal:  bash scripts/lint.sh
#
# Keluar dengan kode 1 bila ada berkas yang gagal php -l, sehingga CI berhenti
# sebelum paket ZIP dibuat dan dirilis.

set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT" || exit 1

if ! command -v php >/dev/null 2>&1; then
  echo "FATAL: php tidak tersedia di PATH" >&2
  exit 1
fi

echo "PHP: $(php -r 'echo PHP_VERSION;')"

mapfile -t FILES < <(find . \
  -path ./node_modules -prune -o \
  -path ./vendor -prune -o \
  -path ./dist -prune -o \
  -path ./.git -prune -o \
  -name '*.php' -type f -print | sort)

if [ "${#FILES[@]}" -eq 0 ]; then
  echo "FATAL: tidak ada berkas PHP yang ditemukan" >&2
  exit 1
fi

echo "Memeriksa ${#FILES[@]} berkas PHP..."
FAILED=0
for f in "${FILES[@]}"; do
  OUT="$(php -l "$f" 2>&1)"
  if [ $? -ne 0 ]; then
    FAILED=$((FAILED + 1))
    echo "FAIL  $f"
    echo "$OUT" | sed 's/^/      /'
  fi
done

if [ "$FAILED" -gt 0 ]; then
  echo ""
  echo "LINT GAGAL: $FAILED berkas bermasalah."
  exit 1
fi

echo "LINT OK: ${#FILES[@]} berkas bersih."
exit 0
