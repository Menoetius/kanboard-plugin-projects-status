<div class="recovery_plan_info flex-row-space-around">
    <div class="flex-grow"> <?= t('Created by') . ': ' . $this->avatar->small($creator['id'], $creator['username'], $creator['name'], $creator['email'], $creator['path'], 'avatar-inline') . $this->text->e($creator['username']); ?></div>
    <p class="flex-grow"> <?= t('Creation date') . ': ' . $this->dt->date($recovery_plan['date']) ?></p>
</div>

<div class="recovery_plan_summary flex-row-space-around">
    <div class="recovery_plan_accomplished flex-column flex-grow">
        <p> <?= t('Accomplished') . ': ' ?> </p>
        <article class="markdown textarea">
            <?= $this->text->markdown($recovery_plan['accomplished']) ?>
        </article>
    </div>
    <div class="recovery_plan_plan flex-column flex-grow">
        <p> <?= t('Plan') . ': ' ?> </p>
        <article class="markdown textarea">
            <?= $this->text->markdown($recovery_plan['plan']) ?>
        </article>
    </div>
</div>