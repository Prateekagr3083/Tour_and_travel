# Tour and Travel Project - Make Fully Functional

## Phase 1: Fix Existing Bugs and Issues
- [x] Fix gender enum case in Register.php (male/female/other vs Male/Female/Other)
- [x] Fix session variables in Login.php (user_name vs first_name/last_name)
- [x] Fix "View Details" button in Tours.php to link to TourDetails.php?id=tour_id
- [x] Fix navbar placeholders (My Profile, My Bookings) with proper links
- [x] Fix TourDetails.php review display (rating vs review_rating)

## Phase 2: Add User Booking Functionality
- [x] Create BookTour.php page for booking tours
- [x] Add booking button to TourDetails.php
- [x] Create user bookings page (MyBookings.php)
- [x] Update navbar to link to MyBookings.php
- [x] Move inline CSS/JS to external files (CSS/BookTour.css, CSS/MyBookings.css, Scripts/BookTour.js, Scripts/MyBookings.js)

## Phase 3: Add User Reviews Functionality
- [x] Add review form to TourDetails.php
- [x] Create process_review.php for submitting reviews
- [x] Update TourDetails.php to show user's own reviews
- [x] Fix reviews display in TourDetails.php (use correct column names)

## Phase 4: Complete Admin Tours Management
- [x] Add edit tour functionality (edit_tour.php, process_edit_tour.php)
- [x] Add delete tour functionality (process_delete_tour.php)
- [x] Add destinations management (Admin/Destinations.php)
- [x] Add tour packages management (Admin/Packages.php)
- [x] Update add_tour_form.php to include destination and package selection

## Phase 5: Add Missing Admin Pages
- [x] Complete Admin/Users.php with full CRUD
- [x] Complete Admin/Bookings.php with booking management
- [x] Complete Admin/Reviews.php with review moderation
- [x] Complete Admin/Payments.php with payment tracking
- [x] Add tour schedules management

## Phase 6: Add User Profile and Terms
- [x] Create UserProfile.php page
- [x] Create Terms.php page
- [x] Update navbar links to point to actual pages

## Phase 7: Session Management Fixes
- [x] Fix automatic logout on page navigation and refresh
- [x] Extend session lifetime to 1 hour
- [x] Add session_config.php to all PHP files
- [x] Update session settings in php.ini

## Phase 8: Testing and Polish
- [ ] Test complete user flow: register -> login -> view tours -> book tour -> add review
- [ ] Test complete admin flow: login -> add tour -> manage bookings -> view reviews
- [ ] Ensure all buttons and links work
- [ ] Add proper error handling and validation
- [ ] Optimize database queries and add indexes if needed
