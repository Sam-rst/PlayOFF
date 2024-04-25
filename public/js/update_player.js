
document.addEventListener('DOMContentLoaded', function () {
    const tournamentInfo = document.getElementById('tournament-info');
    const tournamentId = tournamentInfo.getAttribute('data-tournament-id');

    const removeButtons = document.querySelectorAll('.remove-player');
    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const username = this.getAttribute('data-username');
            const teamIndex = this.getAttribute('data-index'); // S'il est utilisé
            removePlayer(username, teamIndex, tournamentId);
        });
    });
});

function removePlayer(username, teamIndex, tournamentId) {
    fetch(`/tournament/${tournamentId}/remove_player/${username}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Suppression visuelle de l'élément de la liste
                document.querySelector(`li[data-username="${username}"]`).remove();
            } else {
                console.error('Failed to delete the player');
            }
        })
        .catch(error => console.error('Error:', error));
}


document.addEventListener('DOMContentLoaded', function () {
    const tournamentInfo = document.getElementById('tournament-info');
    const tournamentId = tournamentInfo.getAttribute('data-tournament-id');

    const validateButton = document.getElementById('validate-teams');
    if (validateButton) {
        validateButton.addEventListener('click', function () {
            const teamsData = [];
            document.querySelectorAll('.team-box').forEach((teamBox, index) => {
                const teamName = teamBox.querySelector('.team-name').value;
                const playerNames = Array.from(teamBox.querySelectorAll('.player-name')).map(li => li.textContent.trim());
                teamsData.push({ name: teamName, players: playerNames });
            });

            fetch(`/tournament/${tournamentId}/validate_teams`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ teams: teamsData })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url; // Gère la redirection côté client si la réponse a un URL de redirection
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && !data.success) {
                    alert('Erreur lors de l’enregistrement des équipes.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    } else {
        console.error("Validate button not found.");
    }
});


