<?php

function main(): void {
  $matrix = $_POST['matrix'];
  $selected_square = $_POST['selected_square'];
  $player = $_POST['player'];

  // On joue le coup du premier joueur + check s'il gagne
  if ($player === 'true') {
    $returned_coordinate = player_play($selected_square, $matrix);
    echo json_encode($returned_coordinate);
  }

  /*if ($player === 'false') {
    $ia_coordinate = ia_play($matrix);
    echo json_encode($ia_coordinate);
  }*/

}

function player_play($coordinate, $matrix): array {
  $square_coordinate = explode('-', $coordinate);
  $returned_coordinate = '';

  for ($i = 6; $i > -1; $i--) {
    if ($matrix[$i][$square_coordinate[2]] === 'false') {
      $returned_coordinate = $i . '-' .  $square_coordinate[2];
      $matrix[$i][$square_coordinate[2]] = 'player';
      break;
    }
  }

  $player_win = player_win($returned_coordinate, $matrix);

  return [$returned_coordinate, $matrix, $player_win];
}

function ia_play($matrix): array {
  $maximum = -10000;

  $ia_matrix = $matrix;

  for ($i = 6; $i > -1; $i++) {
    for ($j = 0; $j < 7; $j++) {
      if ($ia_matrix[$i][$j] === 'false') {
        $ia_matrix[$i][$j] = 'ia';
        $tmp = minimum_value($ia_matrix, $i . '-' . $j,10 - 1);
        if ($tmp > $maximum) {
          $maximum = $tmp;
          $max_i = $i;
          $max_j = $j;
        }
        $ia_matrix[$i][$j] = 'false';
      }
    }
  }
  $returned_choice = $max_i . '-' . $max_j;
  $matrix[$max_i][$max_j] = 'ia';
  $ia_win = ia_win($returned_choice, $matrix);
  return [$returned_choice, $matrix, $ia_win];
}

function minimum_value($ia_matrix, $coordinates, $depth): int {
  $minimum = 10000;

  if (player_win($coordinates, $ia_matrix) === true) {
    // On évalue
    return evaluate($ia_matrix, 'player');
  }

  if (ia_win($coordinates, $ia_matrix) === true) {
    return evaluate($ia_matrix, 'ia');
  }

  if ($depth === 0) {
    return evaluate($ia_matrix, 'none');
  }

  for ($i = 6; $i > -1; $i++) {
    for ($j = 0; $j < 7; $j++) {
      if ($ia_matrix[$i][$j] === 'false') {
        $ia_matrix[$i][$j] = 'player';
        $tmp = maximum_value($ia_matrix, $i . '-' .  $j, $depth - 1);

        if ($tmp < $minimum) {
          $minimum = $tmp;
        }
        $ia_matrix[$i][$j] = 'false';
      }
    }
  }
  return $minimum;
}

function maximum_value($ia_matrix, $coordinates, $depth) {
  $maximum = -10000;

  if (player_win($coordinates, $ia_matrix) === true) {
    // On évalue
    return evaluate($ia_matrix, 'player');
  }

  if (ia_win($coordinates, $ia_matrix) === true) {
    return evaluate($ia_matrix, 'ia');
  }

  if ($depth === 0) {
    return evaluate($ia_matrix, 'none');
  }

  for ($i = 6; $i > -1; $i++) {
    for ($j = 0; $j < 7; $j++) {
      if ($ia_matrix[$i][$j] === 'false') {
        $ia_matrix[$i][$j] = 'ia';
        $tmp = minimum_value($ia_matrix, $i . '-' .  $j, $depth - 1);

        if ($tmp > $maximum) {
          $maximum = $tmp;
        }
        $ia_matrix[$i][$j] = 'false';
      }
    }
  }
  return $maximum;
}

function evaluate($ia_matrix, $won): int {
  $nb_piece = 0;

  for ($i = 0; $i < 6; $i++) {
    for ($j = 0; $j < 7; $j++) {
      if ($ia_matrix[$i][$j] !== 'false') {
        $nb_piece++;
      }
    }
  }

  if ($won === 'ia') {
    return 1000 - $nb_piece;
  }

  if ($won === 'player') {
    return -1000 + $nb_piece;
  }

  if ($won === 'none') {
    return 0;
  }
}

function player_win($coordinate, $matrix): bool {
  $coordinate = explode('-', $coordinate);
  // On check les cases horizontales
  $checkh = check_horizontal($coordinate, $matrix, 'player');
  if ($checkh === true) {
    return true;
  }

  // On check les cases verticales
  $checkv = check_vertical($coordinate, $matrix, 'player');
  if ($checkv === true) {
    return true;
  }

  // On check les cases diagonales
  $checkd = check_diagonal($coordinate, $matrix, 'player');
  if ($checkd === true) {
    return true;
  }

  return false;
}

function ia_win($coordinate, $matrix): bool {
  $coordinate = explode('-', $coordinate);
  // On check les cases horizontales
  $checkh = check_horizontal($coordinate, $matrix, 'ia');
  if ($checkh === true) {
    return true;
  }

  // On check les cases verticales
  $checkv = check_vertical($coordinate, $matrix, 'ia');
  if ($checkv === true) {
    return true;
  }

  // On check les cases diagonales
  $checkd = check_diagonal($coordinate, $matrix, 'ia');
  if ($checkd === true) {
    return true;
  }

  return false;
}

function check_horizontal($coordinate, $matrix, $checkstring): bool {
  $int_coordinate = (int)$coordinate[1];

  if ($int_coordinate === 0 || $int_coordinate === 1 || $int_coordinate === 2) {
    if ($matrix[$coordinate[0]][$int_coordinate + 1] === $checkstring && $matrix[$coordinate[0]][$int_coordinate + 2] === $checkstring && $matrix[$coordinate[0]][$int_coordinate + 3] === $checkstring) {
      return true;
    }
    return false;
  }

  if ($int_coordinate === 3) {
    if ($matrix[$coordinate[0]][$int_coordinate - 1] === $checkstring && $matrix[$coordinate[0]][$int_coordinate - 2] === $checkstring && $matrix[$coordinate[0]][$int_coordinate - 3] === $checkstring) {
      return true;
    }

    if ($matrix[$coordinate[0]][$int_coordinate + 1] === $checkstring && $matrix[$coordinate[0]][$int_coordinate + 2] === $checkstring && $matrix[$coordinate[0]][$int_coordinate + 3] === $checkstring) {
      return true;
    }
    return false;
  }

  if ($int_coordinate === 4 || $int_coordinate === 5 || $int_coordinate === 6) {
    if ($matrix[$coordinate[0]][$int_coordinate - 1] === $checkstring && $matrix[$coordinate[0]][$int_coordinate - 2] === $checkstring && $matrix[$coordinate[0]][$int_coordinate - 3] === $checkstring) {
      return true;
    }
    return false;
  }
  return false;
}

function check_vertical($coordinate, $matrix, $checkstring): bool {
  $int_coordinate = (int)$coordinate[0];

  if ($int_coordinate === 0 || $int_coordinate === 1 || $int_coordinate === 2) {
    if ($matrix[$int_coordinate + 1][$coordinate[1]] === $checkstring && $matrix[$int_coordinate + 2][$coordinate[1]] === $checkstring && $matrix[$int_coordinate + 3][$coordinate[1]] === $checkstring) {
      return true;
    }
    return false;
  }
  return false;
}

function check_diagonal($coordinate, $matrix, $checkstring): bool {
  $horizontal_coordinate = (int)$coordinate[0];
  $vertical_coordinate = (int)$coordinate[1];

  if ($horizontal_coordinate === 0 || $horizontal_coordinate === 1  || $horizontal_coordinate === 2) {
    if ($vertical_coordinate === 0 || $vertical_coordinate === 1 || $vertical_coordinate === 2) {
      if ($matrix[$horizontal_coordinate + 1][$vertical_coordinate + 1] === $checkstring && $matrix[$horizontal_coordinate + 2][$vertical_coordinate + 2] === $checkstring && $matrix[$horizontal_coordinate + 3][$vertical_coordinate + 3] === $checkstring) {
        return true;
      }
      return false;
    }

    if ($vertical_coordinate === 3) {
      if ($matrix[$horizontal_coordinate + 1][$vertical_coordinate + 1] === $checkstring && $matrix[$horizontal_coordinate + 2][$vertical_coordinate + 2] === $checkstring && $matrix[$horizontal_coordinate + 3][$vertical_coordinate + 3] === $checkstring) {
        return true;
      }

      if ($matrix[$horizontal_coordinate + 1][$vertical_coordinate - 1] === $checkstring && $matrix[$horizontal_coordinate + 2][$vertical_coordinate - 2] === $checkstring && $matrix[$horizontal_coordinate + 3][$vertical_coordinate - 3] === $checkstring) {
        return true;
      }
      return false;
    }

    if ($vertical_coordinate === 4 || $vertical_coordinate === 5 || $vertical_coordinate === 6) {
      if ($matrix[$horizontal_coordinate + 1][$vertical_coordinate - 1] === $checkstring && $matrix[$horizontal_coordinate + 2][$vertical_coordinate - 2] === $checkstring && $matrix[$horizontal_coordinate + 3][$vertical_coordinate - 3] === $checkstring) {
        return true;
      }
      return false;
    }
  }

  if ($horizontal_coordinate === 3 || $horizontal_coordinate === 4  || $horizontal_coordinate === 5) {
    if ($vertical_coordinate === 0 || $vertical_coordinate === 1 || $vertical_coordinate === 2 ||$vertical_coordinate === 3) {
      if ($matrix[$horizontal_coordinate - 1][$vertical_coordinate + 1] === $checkstring && $matrix[$horizontal_coordinate - 2][$vertical_coordinate + 2] === $checkstring && $matrix[$horizontal_coordinate - 3][$vertical_coordinate + 3] === $checkstring) {
        return true;
      }
      return false;
    }

    if ($vertical_coordinate === 4 || $vertical_coordinate === 5 || $vertical_coordinate === 6) {
      if ($matrix[$horizontal_coordinate - 1][$vertical_coordinate - 1] === $checkstring && $matrix[$horizontal_coordinate - 2][$vertical_coordinate - 2] === $checkstring && $matrix[$horizontal_coordinate - 3][$vertical_coordinate - 3] === $checkstring) {
        return true;
      }
      return false;
    }
  }
  return false;
}

main();
