<script>
  export let entries = [];
  export let matchesFilter;
  export let editEntry;
  export let removeEntry;
  export let search;
  export let fromDate;
  export let toDate;
</script>

<!-- Filter -->
<div class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-6 mb-6 shadow-md">
  <h3 class="text-[#a8e0da] text-xl font-bold mb-4">
    <i class="fas fa-filter mr-2"></i>Filter Journal Entries
  </h3>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <input bind:value={search} placeholder="Search..." class="p-3 border-2 border-[#d4c5b9] rounded-lg" />
    <input bind:value={fromDate} type="date" class="p-3 border-2 border-[#d4c5b9] rounded-lg" />
    <input bind:value={toDate} type="date" class="p-3 border-2 border-[#d4c5b9] rounded-lg" />
  </div>
</div>

<!-- List -->
<div class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-2xl p-6 shadow-md">
  <h3 class="text-center text-2xl text-[#a8e0da] font-bold mb-6">
    <i class="fas fa-calendar-alt mr-2"></i>My Journal Entries
  </h3>

  {#if entries.length === 0}
    <p class="text-center text-gray-500">No entries yet.</p>
  {:else}
    {#each entries.filter(matchesFilter) as entry (entry.id)}
      <div class="border-2 border-[#e8d5c4] rounded-xl p-5 mb-5 bg-[#fffaf5] hover:shadow-md transition">
        <div class="flex justify-between items-center mb-3">
          <span class="text-[#a8e0da] font-semibold flex items-center gap-2">
            <i class="fas fa-calendar-alt"></i>
            {new Date(entry.created_at).toLocaleDateString()}
          </span>
          <div class="text-right mt-2 space-x-4">
            <button on:click={() => editEntry(entry)} class="text-sm text-[#a8e0da] font-semibold hover:underline">
              Edit
            </button>
            <button on:click={() => removeEntry(entry.id)} class="text-sm text-red-500 font-semibold hover:underline">
              Delete
            </button>
          </div>
        </div>

        {#if entry.feeling || entry.reason || entry.grateful}
          <div class="border border-[#e8d5c4] rounded-lg p-4 bg-[#fffaf5] mb-3">
            <h4 class="text-[#f4a5a5] font-bold text-lg mb-2 flex items-center gap-2">
              <i class="fas fa-smile"></i> Feelings
            </h4>
            {#if entry.feeling}<p class="text-[#5e5046]"><strong>Feeling:</strong> {entry.feeling}</p>{/if}
            {#if entry.reason}<p class="text-[#7a6e63]"><strong>Reason:</strong> {entry.reason}</p>{/if}
            {#if entry.grateful}<p class="text-[#7a6e63]"><strong>Grateful for:</strong> {entry.grateful}</p>{/if}
          </div>
        {/if}

        {#if entry.mindful}
          <div class="border border-[#e8d5c4] rounded-lg p-4 bg-[#fffaf5] mb-3">
            <h4 class="text-[#a8e0da] font-bold text-lg mb-2 flex items-center gap-2">
              <i class="fas fa-spa"></i> Mindful Exercise
            </h4>
            <p class="text-[#5e5046] whitespace-pre-line">{entry.mindful}</p>
          </div>
        {/if}

        {#if entry.reflection}
          <div class="border border-[#e8d5c4] rounded-lg p-4 bg-[#fffaf5]">
            <h4 class="text-[#b8a59a] font-bold text-lg mb-2 flex items-center gap-2">
              <i class="fas fa-book"></i> Reflection
            </h4>
            <p class="text-[#7a6e63] whitespace-pre-line">{entry.reflection}</p>
          </div>
        {/if}
      </div>
    {/each}
  {/if}
</div>
