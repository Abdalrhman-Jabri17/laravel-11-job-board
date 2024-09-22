<x-layout>
    <x-breadcrumbs class="mb-4" :links="['Jobs' => route('jobs.index'), $job->title => route('jobs.show', $job), 'Apply' => '#']" />

    <x-job-card :$job />

    <x-card>
        <h2 class="mb-4 text-lg font-medium">
            Your job application
        </h2>

        <form action="{{ route('job.application.store', $job) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <x-label :required="true" for="expected_salary">Expected Salary</x-label>
                <x-text-input type="number" name="expected_salary" />
            </div>

            <div class="mb-4">
                <x-label :required="true" for="cv">Upload CV</x-label>
                <x-text-input type="file" name="cv" />
            </div>
            <x-button class="w-full">Apply</x-button>
        </form>
    </x-card>
</x-layout>