# TODO - Menu feature fixes

- [x] Add Restore action for cancelled menus (route + controller + UI)
- [x] Keep user on the same dashboard after status change (Cancel / Draft) using a return parameter
- [x] Update dropdown links in `app/Views/menu/index.php` to pass the return parameter
- [x] Update controller redirects accordingly


- [ ] Quick manual test checklist
  - [ ] Pending dashboard cancel keeps user on /menu/pending
  - [ ] Cancelled dashboard restore moves user to /menu/pending
  - [ ] Active dashboard cancel moves user to /menu (existing behavior)



