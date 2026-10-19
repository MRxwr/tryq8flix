import type { ReactNode } from 'react';
import { IonPage } from '@ionic/react';
import { motion } from 'framer-motion';
import { TopNav } from './TopNav';

interface AppPageProps {
  children: ReactNode;
  /** Set false for full-screen screens such as the player. */
  withNav?: boolean;
  className?: string;
}

export function AppPage({ children, withNav = true, className }: AppPageProps) {
  return (
    <IonPage className={`tv-page${className ? ` ${className}` : ''}`}>
      {withNav && <TopNav />}
      <motion.main
        className="tv-main"
        initial={{ opacity: 0, y: 14 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.35, ease: 'easeOut' }}
      >
        {children}
      </motion.main>
    </IonPage>
  );
}
