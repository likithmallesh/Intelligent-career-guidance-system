// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Example client-side validation for profile form (if needed)
    const profileForm = document.querySelector('.profile-container form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(event) {
            // Basic validation for academic fields
            const major = document.getElementById('major');
            const gpa = document.getElementById('gpa');

            let isValid = true;

            // Example: Major field is optional, but if entered, must be string
            if (major && major.value.trim() !== '' && !/^[a-zA-Z\s,.'-]+$/.test(major.value)) {
                alert('Major/Field of Study contains invalid characters.');
                isValid = false;
            }

            // Example: GPA must be a valid number between 0 and 4
            if (gpa && gpa.value.trim() !== '') {
                const gpaValue = parseFloat(gpa.value);
                if (isNaN(gpaValue) || gpaValue < 0 || gpaValue > 4) {
                    alert('GPA must be a number between 0.00 and 4.00.');
                    isValid = false;
                }
            }

            // Example: Ensure at least one skill is selected (optional rule)
            // const selectedSkills = profileForm.querySelectorAll('input[name="skills[]"]:checked');
            // if (selectedSkills.length === 0) {
            //     alert('Please select at least one skill.');
            //     isValid = false;
            // }

            // Example: Ensure all radio buttons for quiz are selected (if applicable)
            // This is harder for a generic solution, often better done server-side or with specific JS for each quiz group
            const radioGroups = {};
            profileForm.querySelectorAll('input[type="radio"]').forEach(radio => {
                const name = radio.name;
                if (!radioGroups[name]) {
                    radioGroups[name] = false;
                }
                if (radio.checked) {
                    radioGroups[name] = true;
                }
            });

            for (const group in radioGroups) {
                if (!radioGroups[group]) {
                    // alert(`Please answer the question for "${group.replace(/_/g, ' ')}".`);
                    // isValid = false;
                    // break;
                }
            }


            if (!isValid) {
                event.preventDefault(); // Stop form submission if validation fails
            }
        });
    }

    // General purpose functions
    function toggleVisibility(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.style.display = element.style.display === 'none' ? 'block' : 'none';
        }
    }

    // You can add event listeners for multi-step form navigation here
    // For this phase, we are treating it as a single long form.
});
