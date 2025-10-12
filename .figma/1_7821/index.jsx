import React from 'react';

import styles from './index.module.scss';

const Component = () => {
  return (
    <div className={styles.top}>
      <p className={styles.label}>Label</p>
      <div className={styles.inputContent}>
        <p className={styles.error}>Error</p>
      </div>
      <p className={styles.somethingWentWrong}>Something went wrong...</p>
    </div>
  );
}

export default Component;
