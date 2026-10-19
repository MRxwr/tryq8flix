export type Direction = 'up' | 'down' | 'left' | 'right';

export interface Box {
  left: number;
  top: number;
  width: number;
  height: number;
}

const centerOf = (box: Box) => ({ x: box.left + box.width / 2, y: box.top + box.height / 2 });

/**
 * Returns the index of the candidate closest to `current` in `direction`,
 * or -1 when no candidate lies that way.
 *
 * Candidates must sit strictly in the travel direction. The score favours
 * short distances along that axis and penalises drift across it, so moving
 * sideways stays inside the current row and moving down prefers the card
 * directly below.
 */
export function pickNeighbour(current: Box, candidates: Box[], direction: Direction): number {
  const from = centerOf(current);
  let best = -1;
  let bestScore = Number.POSITIVE_INFINITY;

  candidates.forEach((box, index) => {
    const to = centerOf(box);
    const dx = to.x - from.x;
    const dy = to.y - from.y;

    let along = 0;
    let across = 0;
    if (direction === 'right') {
      if (dx <= 1) return;
      along = dx;
      across = Math.abs(dy);
    } else if (direction === 'left') {
      if (dx >= -1) return;
      along = -dx;
      across = Math.abs(dy);
    } else if (direction === 'down') {
      if (dy <= 1) return;
      along = dy;
      across = Math.abs(dx);
    } else {
      if (dy >= -1) return;
      along = -dy;
      across = Math.abs(dx);
    }

    const score = along + across * 2.5;
    if (score < bestScore) {
      bestScore = score;
      best = index;
    }
  });

  return best;
}
