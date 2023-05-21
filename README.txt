
# Things I have added in the provided files.
---
1) Code Refactoring and related files to give a picture of my thoughts regarding the written code
2) Unit tests of two functions only, of BookingController file using mocking to present how I have worked on this side.  

# Regarding Code Refactoring
---
* ***Best Practices***: As laravel is a MVC framework, follow the complete practices of OOP along with some design practices like `repository pattern`, `singleton pattern` `traits` etc. So the best practices includes:
1) To follow the design practices, SOLID principles along with ACID properties on database level. So as far as concerned about the provided files it's not following the SOLID principle, that make it even more worse. 
2) A single function must be of maximum 5-10 lines (like poetry). 
3) Beside following the coding practices (design patterns, solid principles) best practices  also  includes the performance of the application. 
4) Variables should be user friendly.

* ***Good Practices***: The good practices includes:
1) To follow the necessary patterns provided by laravel to make code re-useablity as respository structure has been used in the provided files.
2) Use of proper logging, that is required if a product/project is under developed process.
3) Written test cases

* ***Worst Practices***: Following are the worst practices that I have observed in the provided files:
1) A single file is of more than 2000 lines.
2) A single function is more than 30-40 lines.
3) Variables aren't user friendly, like for a newbie it's can be hard to understand the purpose.
4) Validations were missing for store and update function.
5) Multiple `returns` in single function.
6) Injection of repository in constructor, that is loading all the written function on a single function call. This can create performance issues at some level.
7) Commented code.
---

## Details of  changes I have added.
* To make use of form request.
* Remove respository injection from constructor and added that injection as function parameter from `app/Http/Controllers/BookingController.php`
* Added traits `app/Http/Traits/JobDetailsTrait.php` to make the function minimal and make the code reuseable. I'm assuming that can be helpful when works with the actual code.
* Added a curl request as a helper function in file  `app/Helpers/helper.php`.
* Tried to make variables user friendly at some point in `app/Repository/BookingRepository.php`
* Removed multiple returns from `app/Repository/BookingRepository.php` repository file.
* BookingRepository file can be more optimized by shifting function into their respective models.
* Added unit tests of index and show functions of `app/Http/Controllers/BookingController.php` using mocking.
Near me, by following the mentioned practices this code can be more efficient for newbies and can be more optimize.




