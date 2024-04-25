document.addEventListener('DOMContentLoaded', function() {
    const matchList = document.getElementById('match-list');
    let selectedTeams = [];

    matchList.addEventListener('click', function(e) {
        // Check if the clicked element is a match-team button
        if (e.target.classList.contains('match-team')) {
            e.preventDefault();  // Prevent the form from being submitted
            e.target.classList.toggle('selected');
            manageSelection(e.target);
        }
    });

    function manageSelection(teamElement) {
        const idx = selectedTeams.indexOf(teamElement);
        if (idx > -1) {
            selectedTeams.splice(idx, 1); // Remove from array
        } else {
            selectedTeams.push(teamElement);
            if (selectedTeams.length === 2) {
                swapTeams(selectedTeams[0], selectedTeams[1]);
                selectedTeams.forEach(team => team.classList.remove('selected'));
                selectedTeams = []; // Reset the array
            }
        }
    }

    function swapTeams(team1, team2) {
        // Swap data attributes for backend processing
        const team1Id = team1.getAttribute('data-team-id');
        const team2Id = team2.getAttribute('data-team-id');
        team1.setAttribute('data-team-id', team2Id);
        team2.setAttribute('data-team-id', team1Id);

        // Swap display names
        const team1Name = team1.textContent;
        team1.textContent = team2.textContent;
        team2.textContent = team1Name;
    }
});
