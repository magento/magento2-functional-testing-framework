# Interactive Pause

It can be difficut to write a successful test on the first attempt. You will need to try different commands, with different arguments, before you find the correct path.

Since Codeception 3.0, you can pause execution in any point and enter an interactive shell where you will be able to try commands in action. 

Now this `Interactive Pause` feature is available in MFTF. All you need to do is to set `ENABLE_PAUSE=true` in `.env`.

Check [pause on codeception.com][] for documentation and a video to see `Interactive Pause` in action.
 
In short, when a test gets to `$I->pause()` step, it stops and shows a console where you can try all available commands with auto-completion, stash commands, save screenshots, etc. 

## Generation Time

A `<pause>` action in xml will always be generated into php regardless if `ENABLE_PAUSE=true` is set or not. 
However, when `ENABLE_PAUSE=true` is set, an additional `pause()` action will be generated in `_failed()` hook for a test,
so that the test may pause on failure at run time.

## Execution Time

To use `Interactive Pause` at run time, there are two types of MFTF commands to use:

### MFTF Run Commands

When `ENABLE_PAUSE=true` is set, the following MFTF run commands support `Interactive Pause`.

```bash
vendor/bin/mftf run:group
```

```bash
vendor/bin/mftf run:test
```

```bash
vendor/bin/mftf run:manifest
```

```bash
vendor/bin/mftf run:failed
```

### MFTF Codecept Run Command

You can also use MFTF's wrapper command to run Codeception directly and activate `Interactive Pause` by passing `--debug` option. 
You do not need to set `ENABLE_PAUSE=true` for this command.

```bash
vendor/bin/mftf codecept:run --debug
```

<div class="bs-callout-warning">
Note: MFTF run command's `--debug` option is different from Codeception `--debug` mode option. 
</div>

<div class="bs-callout-warning">
Note: You may want to limit the usage of this Codeception command with arguments and options for `acceptance` only, since it is what is supported by MFTF. You should also change `acceptance` to `functional` when using this command when referring to Codeception documentation.
</div>

## References

[pause on codeception.com](https://codeception.com/docs/02-GettingStarted#Interactive-Pause)
