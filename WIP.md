# MVP
- [x] Configure profiles with global "mutate()" function in Pest.php
- [x] Override profile configuration in CLI
- [ ] Run mutation tests in CLI
- [ ] Configure and run mutation tests by appending "->mutate()" to a test or describe block
- [ ] Support xdebug and pcov
- [ ] Comprehensive sets of mutators, reasonable default set
- [ ] paths()
- [ ] mutators() / except()
- [ ] coveredOnly()
- [ ] uncommittedOnly()
- [ ] changedOnly()
- [ ] stopOnSurvival()
- [ ] stopOnUncovered()
- [ ] Parallel support
- [ ] Minimum Threshold Enforcement
- [ ] Allow registering Custom Mutators
- [ ] Disable mutations by annotation
- [ ] Caching
- [ ] Prioritize tests to execute (fast tests first, etc.)
- [ ] Verbose output
- [ ] Text log
- [ ] HTML report
- [ ] Automatically skip "Arch" tests
- [ ] Awesome docs: "Why to use" and "How to use"

# Current Tasks
- [ ] `HandleTestCallProfileConfigurationTest` has side effects on other tests, if they should be run with `--mutate`
- [ ] `->mutate()` should not override `--covered-only=false`
- [ ] Set proper default paths `src` or `app`  if Laravel. see src/Plugins/Parallel/Handlers/Laravel.php:29

# Backlog Prio 1
- [ ] Automatically create documentation from mutators
- [ ] Add more mutators and sets
- [ ] Create a sensible default set

# Backlog Prio 2
- [ ] Dedicated help output (`vendor/bin/pest --mutate --help`)
- [ ] Add help to show available mutators and sets

# Backlog Future Release
- [ ] 

# Notes
## Running Initial Test Suite in Parallel
### Problem 1
The Parallel Plugin does exit and does not fire a TestSuite->Finished event. Therefore the only hook is "AddsOutput", but this is a bit weak.
### Problem 2
PHP Coverage Report does not contain infos about which tests covered a specific line.
