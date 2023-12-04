# MVP
- [x] Configure profiles with global "mutate()" function in Pest.php
- [x] Override profile configuration in CLI
- [x] Run mutation tests in CLI
- [x] Configure and run mutation tests by appending "->mutate()" to a test or describe block
- [ ] Support xdebug (not tested yet, but should already work)
- [x] Support pcov
- [x] Comprehensive sets of mutators, reasonable default set
- [x] paths()
- [x] mutators()
- [x] except()
- [x] coveredOnly()
- [x] uncommittedOnly()
- [x] changedOnly()
- [x] bail()
- [x] stopOnSurvived()
- [x] stopOnNotCovered()
- [x] Minimum Threshold Enforcement
- [ ] Allow registering Custom Mutators
- [ ] Disable mutations by annotation
- [x] Caching
- [ ] Order mutations to execute
- [ ] Verbose output
- [ ] Text log
- [ ] HTML report
- [ ] Automatically skip "Arch" tests
- [ ] Awesome docs

# Known Bugs

# Backlog Prio 1
- [ ] Run test that killed a mutation first
- [ ] Run mutations in a reasonable order: New, Survived, NotCovered, Skipped, Killed (Survived first, if --stop-on-survived or --bail; NotCovered first, if --stop-on-uncovered)
- [ ] Automatically empty cache when package version changes / Maybe there is another approach: Use the same cache key per php file, but store a hash of the file content and the package version in the cache. If the hash changes, the cache is invalid.
- [ ] Automatically skip "Arch" and "Stressless" tests - wait for arch() and stress() aliases for test()
- [ ] Properly support xdebug
- [ ] What should we do with interfaces? ignore them completely?
- [ ] Log to file

# Backlog Prio 2
- [ ] Add mutator to unwrap idn_to_ascii() and idn_to_utf8()
- [ ] Add array declaration mutators: https://stryker-mutator.io/docs/mutation-testing-elements/supported-mutators/#array-declaration
- [ ] Add empty block statement mutator?: https://stryker-mutator.io/docs/mutation-testing-elements/supported-mutators/#array-declaration
- [ ] Check if we have mutators which do the same mutation. For example: "true" to "false", and "return true" to "return false"
- [ ] Make the output cristal clear that "survived" is bad and "killed" is good. Maybe change the wording to "missed" and "detected"? What do other libraries to work around the confusion? https://x.com/tfidry/status/1719293281568215499?s=46&t=DEd0fniSoLaUYk0rCiXljw
- [ ] Dedicated help output (`vendor/bin/pest --mutate --help`)
- [ ] Add help to show available mutators and sets
- [ ] Add more Laravel mutators
- [ ] Improve test filtering. Some test names, may not work
- [ ] Better loop detection. For example when mutate break to continue in a while true loop
- [ ] Beautify output
- [ ] HTML report

# Backlog Future Release
- [ ] Allow to pass a custom cache instance mutate()->cache(MyRedisCache::getInstance())
- [ ] Automatically convert Infection configuration to Pest configuration

# Notes
## Running Initial Test Suite in Parallel
### Problem 1
The Parallel Plugin does exit and does not fire a TestSuite->Finished event. Therefore the only hook is "AddsOutput", but this is a bit weak.
### Problem 2
PHP Coverage Report does not contain infos about which tests covered a specific line.
