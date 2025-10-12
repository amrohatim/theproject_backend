import React from 'react';

import styles from './index.module.scss';

const Component = () => {
  return (
    <div className={styles.input}>
      <p className={styles.label}>Label</p>
      <div className={styles.content}>
        <div className={styles.left}>
          <p className={styles.valid}>Valid</p>
        </div>
        <div className={styles.check}>
          <img src="../image/mgnge9bv-6wsa079.svg" className={styles.uCheck} />
        </div>
      </div>
    </div>
  );
}

export default Component;
