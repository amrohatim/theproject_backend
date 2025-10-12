import React from 'react';

import styles from './index.module.scss';

const Component = () => {
  return (
    <div className={styles.input}>
      <p className={styles.label}>Label</p>
      <div className={styles.inputContent}>
        <p className={styles.filled}>Filled</p>
      </div>
    </div>
  );
}

export default Component;
