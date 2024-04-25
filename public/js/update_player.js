
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




