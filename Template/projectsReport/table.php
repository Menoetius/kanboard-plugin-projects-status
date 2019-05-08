<?php if (!empty($data)): ?>
    <button class="export exportCSV btn-blue"><?= t('Export table to CSV') ?></button>
    <button class="export exportHTML btn-blue"><?= t('Export table as HTML') ?></button>
    <table class="report-table table-striped table-scrolling">

        <tr>
            <th class="column"><?= t('Status') ?></th>
            <th class="column"><?= t('Name') ?></th>
            <th class="column"><?= t('End date') ?></th>
            <th class="column"><?= t('Accomplished') ?></th>
            <th class="column"><?= t('Planned') ?></th>
            <th class="column"><?= t('Recovery plan date') ?></th>
            <th class="column" colspan="6"><?= t('Issues') ?></th>
        </tr>
        <?php foreach ($data as $row): ?>
            <tr>

                <?php if ($row['project_status'] == 0): ?>
                    <td class="green"><?= t('Good') ?></td>
                <?php elseif ($row['project_status'] == 1): ?>
                    <td class="yellow"><?= t('Warning') ?></td>
                <?php elseif ($row['project_status'] == 2): ?>
                    <td class="red"><?= t('Bad') ?></td>
                <?php endif ?>

                <td>
                    <?= $row['project_name'] ?>
                </td>
                <td>
                    <?= $this->dt->date($row['end_date']) ?>
                </td>
                <td>
                    <?= $row['accomplished'] ?>
                </td>
                <td>
                    <?= $row['plan'] ?>
                </td>
                <td>
                    <?= $this->dt->date($row['date']) ?>
                </td>
                <th class="column"><?= t('Issue name') ?></th>
                <th class="column"><?= t('Creator') ?></th>
                <th class="column"><?= t('Due date') ?></th>
                <th class="column"><?= t('Referenced Task ') ?></th>
                <th class="column"><?= t('Priority') ?></th>
                <th class="column"><?= t('Completed') ?></th>
            </tr>
            <?php if (!empty($issues[$row['id']])): ?>
                <?php foreach ($issues[$row['id']] as $issue): ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <?= $issue['name'] ?>
                        </td>
                        <td>
                            <?= $issue['username'] ?>
                        </td>
                        <td>
                            <?= $this->dt->date($issue['due_date']) ?>
                        </td>
                        <td>
                            <?= $issue['title'] ?>
                        </td>
                        <td>
                            <?= $issue['priority'] ?>
                        </td>
                        <td>
                            <?php if ($issue['completed']): ?>
                                <?= t('Yes') ?>
                            <?php else: ?>
                                <?= t('No') ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
            <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <!--            <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>-->
        <?php endforeach ?>
    </table>
<?php else: ?>
    <p><?= t('No data to show.') ?></p>
<?php endif ?>