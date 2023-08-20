#!/bin/bash

zstd -9 "$(dirname "$0")"/testdata/random_strings
