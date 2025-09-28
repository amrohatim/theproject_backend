import React from 'react';

import styles from './index.module.scss';

const Component = () => {
  return (
    <div className={styles.bgColorRectangle}>
      <div className={styles.label2}>
        <p className={styles.label}>Label</p>
      </div>
      <p className={styles.aSubtitle}>Sea turtle</p>
      <p className={styles.aLoremIpsumDolorSitA}>
        Sea turtles are reptiles of the order Testudines
      </p>
    </div>
  );
}

export default Component;
