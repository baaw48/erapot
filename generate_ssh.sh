#!/bin/bash
# Auto generate SSH key without passphrase
ssh-keygen -t rsa -b 4096 -C "ciputra.bahtiar@gmail.com" -f "$HOME/.ssh/id_rsa" << EOF

EOF