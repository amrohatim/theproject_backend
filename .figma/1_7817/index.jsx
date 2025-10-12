import React from 'react';

import styles from './index.module.scss';

const Component = () => {
  return (
    <div className={styles.input}>
      <p className={styles.label}>Label</p>
      <div className={styles.inputContent}>
        <p className={styles.typing}>Typing |</p>
      </div>
    </div>
  );
}

export default Component;
