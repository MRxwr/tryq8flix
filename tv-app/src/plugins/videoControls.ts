import { registerPlugin } from '@capacitor/core';

interface VideoControlsPlugin {
  sendMediaKey(options: { action: 'play' | 'pause' }): Promise<void>;
}

export const videoControls = registerPlugin<VideoControlsPlugin>('VideoControls');
