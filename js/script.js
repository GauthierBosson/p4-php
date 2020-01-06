$(window).on('load', function () {
  const gameContainer = document.querySelector('#game');

  // Création de la matrice pour initialisation du jeu
  // Cette représentation de la matrice servira aussi à être passé au PHP afin d'avoir une représentation du jeu côté serveur
  // et ainsi faire en sorte que le PHP puisse aussi modifier le jeu
  let gameSquares = Array(6).fill(null).map(() => Array(7).fill("false"));

  console.table(gameSquares);

  // Affichage du jeu
  // L'idée est de passer les coordonnées de chaque case dans la seconde classe CSS afin de pouvoir l'extraire facilement
  for (let i = 0; i < 6; i++) {
    for (let j = 0; j < 7; j++) {
      gameContainer.insertAdjacentHTML('beforeend', `<div class="squares square-${i + '-' + j}"></div>`);
    }
  }

  // Fonction ajax
  // Utilisation d'ajax pour éviter les rechargements de pages inutiles et donc combler au problème de tout faire en PHP
  // (Sauvegarder le jeu en session par exemple)
  $(".squares").on('click', function (e) {
    const selectedSquare = e.target.classList[1];
    $.ajax({
      type: 'POST',
      url: 'php/play.php',
      data: { matrix: gameSquares, selected_square: selectedSquare, player: true },
      success: function (data) {
        const result = JSON.parse(data);
        console.table(result[1]);
        console.log(result);
        gameSquares = result[1];

        // On insert le choix du joueur
        const coordinateToInsert = document.querySelector(`.square-${result[0]}`);
        const playerPlay = document.createElement('div');
        playerPlay.classList.add('red');
        playerPlay.classList.add(`.square-${result[0]}`);
        coordinateToInsert.append(playerPlay);

        // On fait jouer l'ia
        // EN COURS
        /*$.ajax({
          type: 'POST',
          url: 'php/play.php',
          data: { matrix: gameSquares, playerPlay: false },
          success: function (data) {
            const result = data;
            //console.table(result[1]);
            console.log(result);
            gameSquares = result[1];

            // On insert le choix de l'ia
            const coordinateToInsert = document.querySelector(`.square-${result[0]}`);
            const iaPlay = document.createElement('div');
            iaPlay.classList.add('yellow');
            iaPlay.classList.add(`.square-${result[0]}`);
            coordinateToInsert.append(iaPlay);
          },
          error: function () {
            console.log('error')
          }
        })*/
      },
      error: function () {
        console.log('error')
      }
    })
  })
});
