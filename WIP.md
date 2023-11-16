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
- [ ] except()
- [x] coveredOnly()
- [ ] uncommittedOnly()
- [ ] changedOnly()
- [x] bail()
- [x] stopOnSurvived()
- [x] stopOnNotCovered()
- [ ] Minimum Threshold Enforcement
- [ ] Allow registering Custom Mutators
- [ ] Disable mutations by annotation
- [ ] Caching
- [ ] Prioritize tests to execute (fast tests first, etc.)
- [ ] Verbose output
- [ ] Text log
- [ ] HTML report
- [ ] Automatically skip "Arch" tests
- [ ] Awesome docs

# Backlog Prio 1
- [ ] Improve test filtering. Some test names, may not work
- [ ] Better loop detection. For example when mutate break to continue in a while true loop
- [ ] Automatically create documentation from mutators
- [ ] Create a sensible default set

# Backlog Prio 2
- [ ] Sort test runs to run quick tests first; and if available the test a mutation killed in a previous run
- [ ] Beautify output / respect --compact option
- [ ] Add more Laravel mutators
- [ ] Allow to pass a custom cache instance mutate()->cache(MyRedisCache::getInstance())
- [ ] Dedicated help output (`vendor/bin/pest --mutate --help`)
- [ ] Add help to show available mutators and sets
- [ ] Make the output cristal clear that "survived" is bad and "killed" is good. Maybe change the wording to "missed" and "detected"? What do other libraries to work around the confusion? https://x.com/tfidry/status/1719293281568215499?s=46&t=DEd0fniSoLaUYk0rCiXljw

# Backlog Future Release
- [ ] 

# Notes
## Running Initial Test Suite in Parallel
### Problem 1
The Parallel Plugin does exit and does not fire a TestSuite->Finished event. Therefore the only hook is "AddsOutput", but this is a bit weak.
### Problem 2
PHP Coverage Report does not contain infos about which tests covered a specific line.
