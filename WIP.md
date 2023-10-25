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
- [ ] test()->mutate()

# Backlog Prio 1
- [ ] Create documentation form mutators
- [ ] Add more mutators and sets
- [ ] Create a sensible default set

# Backlog Prio 2
- [ ] Dedicated help output (`vendor/bin/pest --mutate --help`)
- [ ] Add help to show available mutators and sets

# Backlog Future Release
- [ ] 
