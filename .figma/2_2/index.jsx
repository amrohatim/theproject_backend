import React from 'react';

import styles from './index.module.scss';

const Component = () => {
  return (
    <div className={styles.component1}>
      <div className={styles.rectangle3}>
        <p className={styles.a01}>01</p>
      </div>
      <div className={styles.rectangle4}>
        <p className={styles.a01}>02</p>
      </div>
      <div className={styles.rectangle5}>
        <p className={styles.a01}>03</p>
      </div>
      <div className={styles.rectangle32}>
        <p className={styles.a01}>04</p>
      </div>
      <div className={styles.rectangle5}>
        <p className={styles.a01}>05</p>
      </div>
    </div>
  );
}

export default Component;
