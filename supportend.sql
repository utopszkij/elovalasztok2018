
/* támogatottság sorrend, 3. helyen lévő támogatottsága   --> 3*/
SELECT proposal_id, COUNT(user_id) cc
FROM tst_supports
GROUP BY proposal_id
ORDER BY 2 DESC;

/* munkafile létrehozása */
DROP TABLE IF EXISTS tst_supportWork2;
CREATE TABLE tst_supportwork2
SELECT proposal_id, COUNT(user_id) cc
FROM  tst_supports
GROUP BY proposal_id
HAVING COUNT(user_id) >= 3
ORDER BY 2 DESC;

/* test work table */
SELECT * FROM tst_supportwork2;

/* proposal --> candidate modositás */
UPDATE tst_content c, tst_supportwork2 w
SET catid = 1
WHERE c.id = w.proposal_id;

/* ellenörzés */
SELECT * FROM tst_content;

