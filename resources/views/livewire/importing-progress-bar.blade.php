<div class="importing-progress-bar">
    <div class="progress" style="height: 30px">
        <div {{ !session('importing') ?: 'wire:poll.100ms' }} class="progress-bar bg-dark" id="progress-bar" style="width: {{ $progress }}%" role="progressbar bg-dark"
            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            {{ $progress }}%
        </div>
    </div>
</div>
