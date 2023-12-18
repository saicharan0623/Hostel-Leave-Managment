// Validate the leave application form
function validateLeaveApplicationForm() {
  var school = document.forms["leaveApplicationForm"]["school"].value;
  var name = document.forms["leaveApplicationForm"]["name"].value;
  var fromDate = document.forms["leaveApplicationForm"]["from_date"].value;
  var toDate = document.forms["leaveApplicationForm"]["to_date"].value;
  var reason = document.forms["leaveApplicationForm"]["reason"].value;

  // Perform your validation checks here
  if (school === "") {
    alert("Please select a school.");
    return false;
  }

  if (name === "") {
    alert("Please enter your name.");
    return false;
  }

  if (fromDate === "" || toDate === "") {
    alert("Please select both the from and to dates.");
    return false;
  }

  if (reason === "") {
    alert("Please enter a reason for leave.");
    return false;
  }

  return true; // Return true if all validation checks pass
}

// Add event listener to the leave application form
var leaveApplicationForm = document.getElementById("leaveApplicationForm");
if (leaveApplicationForm) {
  leaveApplicationForm.addEventListener("submit", function (event) {
    event.preventDefault();
    if (validateLeaveApplicationForm()) {
      // Submit the form via AJAX or perform any other required actions
      this.submit();
    }
  });
}
