#!/usr/bin/env bash
#
# Jalankan seluruh harness uji di tests/.
#
# Setiap harness mengembalikan exit code != 0 bila assertion gagal,
# sehingga skrip ini bisa dipakai sebagai gate CI.
#
# Pemakaian lokal:  bash scripts/test.sh

set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT" || exit 1

if ! command -v php >/dev/null 2>&1; then
  echo "FATAL: php tidak tersedia di PATH" >&2
  exit 1
fi

shopt -s nullglob
TESTS=(tests/*.test.php)
shopt -u nullglob

if [ "${#TESTS[@]}" -eq 0 ]; then
  echo "Tidak ada berkas tests/*.test.php — dilewati."
  exit 0
fi

FAILED=0
for t in "${TESTS[@]}"; do
  echo "=============================================================="
  echo ">> $t"
  echo "=============================================================="
  php "$t"
  RC=$?
  if [ "$RC" -ne 0 ]; then
    FAILED=$((FAILED + 1))
    echo ">> GAGAL: $t (exit $RC)"
  fi
done

echo ""
if [ "$FAILED" -gt 0 ]; then
  echo "TEST GAGAL: $FAILED dari ${#TESTS[@]} harness."
  exit 1
fi

echo "TEST OK: ${#TESTS[@]} harness lulus."
exit 0
