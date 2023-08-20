#!/bin/sh

script_dir=$(dirname "$0")

if [ ! -f "$script_dir"/testdata/random_strings ]; then
    zstd -d "$script_dir"/testdata/random_strings.zst
fi

php "$script_dir"/test.php | go run go/cmd/tests/main.go
