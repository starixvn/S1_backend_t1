SHELL_DIR="$(dirname $0)"

echo "$SHELL_DIR/../../"

cd $SHELL_DIR/../../
bash run_cli.sh -D -b $1 switch_payment_lib
