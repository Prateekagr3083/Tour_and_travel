# TODO: Fix Save Tour Button in Admin Add Tour Page

## Overview
Fix the issue where the "Save Tour" button does not submit the form to the backend. This involves updating the form to include required fields (destination and package selects), fixing duplicates, modifying JS to allow actual submission, and ensuring backend compatibility.

## Steps

1. **Update Admin/add_tour_form.php** [x]
   - Include database connection (../Database/db_connect.php).
   - Fix duplicate location field: Keep the first as name="location" (text input for specific location), change the second (id="destination") to a <select name="destination_id"> populated with options from 'destinations' table (value=id, text=name). If no destinations, show placeholder.
   - Add a new <select name="package_id"> after duration, populated from 'tour_packages' table (value=id, text=name).
   - Ensure all fields are required where appropriate.
   - Keep image upload and other fields intact.

2. **Update Admin/js/add_tour.js** [x]
   - In the submit event listener: Move e.preventDefault() to only trigger on validation failure (inside if blocks for errors).
   - Update validation to include checks for destination_id and package_id selects (e.g., if (parseInt(document.getElementById('destination_id').value) <= 0) { alert... }).
   - On success, do not prevent default – let the form submit naturally to process_add_tour.php. Remove the fake alert and manual hide/reset (backend will redirect).
   - Optionally, add a loading state or disable button during submit, but keep simple.

3. **Review and Minor Update Admin/process_add_tour.php** [x]
   - No major changes needed, but ensure it handles the new selects (already does via $_POST['destination_id'] and $_POST['package_id']).
   - If keeping a separate "destination_name" text, but backend uses 'location' for that – map accordingly (current plan uses location as text, destination_id as FK).
   - Confirm image upload path and DB inserts.

4. **Test the Implementation** [x]
   - Use browser_action to load http://localhost/Tour_and_travel/Admin/Tours.php (assume XAMPP running on port 80).
   - Click "Add New Tour", fill form (select destination/package, upload image), submit.
   - Verify: Redirect to Tours.php with success message, new tour appears in table.
   - If errors (e.g., no DB data for selects), insert sample data via SQL command.
   - Check for JS console errors or PHP errors.

5. **Update TODO.md** [x]
   - Mark steps as completed after each.

## Dependencies
- Database tables: tours, destinations (id, name), tour_packages (id, name), tour_images (tour_id, image_url, description).
- XAMPP/PHP/MySQL running.
- Admin logged in.

## Completion Criteria
- Save Tour button submits form, processes via backend, adds tour to DB, shows success, and lists new tour.
