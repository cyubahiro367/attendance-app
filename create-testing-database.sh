#!/usr/bin/env bash

mysql --user=root --password=root <<-EOSQL
    CREATE DATABASE IF NOT EXISTS testing;
EOSQL