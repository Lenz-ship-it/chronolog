<script>
  import { fade, fly } from 'svelte/transition';
  import { createEventDispatcher } from 'svelte';
  import { onMount } from 'svelte';

  const dispatch = createEventDispatcher();

  export let selectedDate = '';

  let today = new Date();
  let todayStr = today.toISOString().split("T")[0];
  let currentMonth = today.getMonth();
  let currentYear = today.getFullYear();
  let days = [];

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  let years = [];
  for (let y = currentYear - 50; y <= currentYear + 50; y++) {
    years.push(y);
  }

  onMount(() => {
    generateCalendar(currentMonth, currentYear);

    selectedDate = todayStr;
  });

  onMount(() => generateCalendar(currentMonth, currentYear));

  function generateCalendar(month, year) {
    const firstDay = new Date(year, month).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    days = [];

    for (let i = 0; i < firstDay; i++) days.push(null);

    for (let d = 1; d <= daysInMonth; d++) {
      const date = new Date(year, month, d);
      const dateStr = date.toISOString().split('T')[0];
      days.push(dateStr);
    }
  }

  function prevMonth() {
    if (currentMonth === 0) {
      currentMonth = 11;
      currentYear--;
    } else currentMonth--;

    generateCalendar(currentMonth, currentYear);
  }

  function nextMonth() {
    if (currentMonth === 11) {
      currentMonth = 0;
      currentYear++;
    } else currentMonth++;

    generateCalendar(currentMonth, currentYear);
  }

  function onSelectMonth(e) {
    currentMonth = +e.target.value;
    generateCalendar(currentMonth, currentYear);
  }

  function onSelectYear(e) {
    currentYear = +e.target.value;
    generateCalendar(currentMonth, currentYear);
  }

  function selectDate(dateStr) {
    selectedDate = dateStr;
  }

  function confirmDate() {
    if (selectedDate) dispatch('confirm', selectedDate);
  }
</script>

<div
  in:fade={{ duration: 600 }}
  out:fly={{ y: -60, duration: 600 }}
  class="fixed inset-0 flex items-center justify-center bg-linear-to-br from-[#a8e0da] to-[#b8dbd9] z-50"
>
  <div class="bg-[#fef9f3] border-4 border-[#e8d5c4] rounded-3xl shadow-xl p-8 text-center w-[90%] max-w-lg">

    <h2 class="text-3xl font-bold text-[#5e5046] mb-6">📅 Choose Your Journal Date</h2>

    <div class="flex items-center justify-between mb-6">
      <button
        on:click={prevMonth}
        class="text-[#a8e0da] text-3xl font-bold hover:text-[#f4a5a5] transition px-2"
      >
        ‹
      </button>

      <div class="flex items-center gap-3">
        <select
          bind:value={currentMonth}
          on:change={onSelectMonth}
          class="bg-[#fef9f3] border-2 border-[#e8d5c4] rounded-xl px-3 py-2 font-semibold text-[#5e5046]"
        >
          {#each monthNames as m, i}
            <option value={i}>{m}</option>
          {/each}
        </select>

        <select
          bind:value={currentYear}
          on:change={onSelectYear}
          class="bg-[#fef9f3] border-2 border-[#e8d5c4] rounded-xl px-3 py-2 font-semibold text-[#5e5046]"
        >
          {#each years as y}
            <option value={y}>{y}</option>
          {/each}
        </select>
      </div>

      <button
        on:click={nextMonth}
        class="text-[#a8e0da] text-3xl font-bold hover:text-[#f4a5a5] transition px-2"
      >
        ›
      </button>
    </div>

    <div class="grid grid-cols-7 gap-2 mb-3 text-[#5e5046] font-semibold">
      <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
    </div>

    <div class="grid grid-cols-7 gap-2">
      {#each days as date}
        {#if date}
          <button
            class="
              w-10 h-10 rounded-full flex items-center justify-center text-[#5e5046] font-medium 
              transition-all duration-200 
              hover:bg-[#a8e0da]/50 hover:scale-105

              {selectedDate === date ? 'bg-[#a8e0da] text-white scale-105 font-bold shadow-md' : ''}
              {todayStr === date && selectedDate !== date ? 'ring-2 ring-[#f4a5a5] ring-offset-2 ring-offset-[#fef9f3]' : ''}
            "
            on:click={() => selectDate(date)}
          >
            {new Date(date).getDate()}
          </button>
        {:else}
          <div class="w-10 h-10"></div>
        {/if}
      {/each}
    </div>


    <div class="mt-8">
      <button
        on:click={confirmDate}
        disabled={!selectedDate}
        class="px-8 py-3 rounded-full font-bold text-white text-lg transition-all hover:scale-105 disabled:opacity-50"
        style="background: linear-gradient(135deg, #a8e0da 0%, #f4a5a5 100%); box-shadow: 0 4px 15px rgba(0,0,0,0.2);"
      >
        <i class="fas fa-check mr-2"></i> Continue
      </button>
    </div>

  </div>
</div>

<style>
  select {
    cursor: pointer;
    padding-right: 30px;
  }
</style>
