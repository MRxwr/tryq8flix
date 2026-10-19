import { describe, expect, it } from 'vitest';
import { pickNeighbour, type Box } from './spatialNav';

const box = (left: number, top: number, width = 100, height = 100): Box => ({ left, top, width, height });

describe('pickNeighbour', () => {
  const current = box(200, 200);

  it('moves to the card directly to the right', () => {
    const candidates = [box(400, 200), box(200, 400)];
    expect(pickNeighbour(current, candidates, 'right')).toBe(0);
  });

  it('moves to the card directly below rather than one diagonally further away', () => {
    const candidates = [box(500, 420), box(205, 420)];
    expect(pickNeighbour(current, candidates, 'down')).toBe(1);
  });

  it('ignores candidates that are not in the travel direction', () => {
    const candidates = [box(0, 200)];
    expect(pickNeighbour(current, candidates, 'right')).toBe(-1);
  });

  it('prefers staying in the same row when moving sideways', () => {
    const candidates = [box(420, 600), box(420, 205)];
    expect(pickNeighbour(current, candidates, 'right')).toBe(1);
  });

  it('returns -1 when there are no candidates', () => {
    expect(pickNeighbour(current, [], 'up')).toBe(-1);
  });
});
