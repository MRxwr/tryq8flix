import { motion } from 'framer-motion';

export function RowSkeleton({ count = 6 }: { count?: number }) {
  return (
    <>
      {Array.from({ length: count }, (_, index) => (
        <div key={index} className="skeleton-card" aria-hidden="true" />
      ))}
    </>
  );
}

export function GridSkeleton({ count = 12 }: { count?: number }) {
  return (
    <div className="poster-grid" aria-busy="true">
      {Array.from({ length: count }, (_, index) => (
        <div key={index} className="skeleton-card" aria-hidden="true" />
      ))}
    </div>
  );
}

export function Spinner({ label = 'Loading' }: { label?: string }) {
  return (
    <div className="spinner" role="status" aria-label={label}>
      <motion.span
        className="spinner__ring"
        animate={{ rotate: 360 }}
        transition={{ duration: 0.9, repeat: Infinity, ease: 'linear' }}
      />
    </div>
  );
}

export function ErrorState({
  message,
  onRetry,
  compact = false,
}: {
  message: string | null;
  onRetry?: () => void;
  compact?: boolean;
}) {
  return (
    <div className={`state state--error${compact ? ' state--compact' : ''}`} role="alert">
      <p>{message ?? 'Something went wrong.'}</p>
      {onRetry ? (
        <button type="button" className="btn" data-tv onClick={onRetry}>
          Try again
        </button>
      ) : null}
    </div>
  );
}

export function EmptyState({ text }: { text: string }) {
  return (
    <div className="state">
      <p>{text}</p>
    </div>
  );
}
