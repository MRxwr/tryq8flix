import type { ReactNode } from 'react';
import { AnimatePresence, motion } from 'framer-motion';

interface HeroProps {
  title: string;
  description?: string;
  backdrop?: string;
  meta?: string;
  actions?: ReactNode;
}

export function Hero({ title, description, backdrop, meta, actions }: HeroProps) {
  const key = backdrop ?? 'none';

  return (
    <section className="hero">
      <AnimatePresence initial={false}>
        <motion.div
          key={key}
          className="hero__bg"
          style={backdrop ? { backgroundImage: `url("${backdrop}")` } : undefined}
          initial={{ opacity: 0, scale: 1.05 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.9, ease: 'easeOut' }}
        />
      </AnimatePresence>
      <motion.div
        key={title}
        className="hero__content"
        initial={{ opacity: 0, y: 24 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.55, delay: 0.1, ease: 'easeOut' }}
      >
        {meta ? <p className="hero__meta">{meta}</p> : null}
        <h1 className="hero__title">{title}</h1>
        {description ? <p className="hero__desc">{description}</p> : null}
        {actions ? <div className="hero__actions">{actions}</div> : null}
      </motion.div>
    </section>
  );
}
