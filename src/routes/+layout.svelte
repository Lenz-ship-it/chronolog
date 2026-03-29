<script>
	import '../app.css';
	import favicon from '$lib/assets/favicon.svg';
	import music from '$lib/assets/music/instrument.mp3';
	import { onMount } from 'svelte';
	let { children } = $props();
	
	let audio;
	let isPlaying = $state(true);
	onMount(() => {
		audio = new Audio(music);
		audio.loop = true;
		audio.volume = 0.5;

		function enableAudio() {
			audio.play();
			window.removeEventListener("click", enableAudio);
		}

		window.addEventListener("click", enableAudio);
	});


	function toggleMusic() {
		if (!audio) return;
		if (isPlaying) audio.pause();
		else audio.play();
		isPlaying = !isPlaying; 
	}
</script>

<svelte:head>
	<title>Strength Fear Tracker</title>
	<link rel="icon" href={favicon} />
</svelte:head>

<button class="music-btn" onclick={toggleMusic} aria-label="Toggle music">
	{#if isPlaying}
		<i class="fas fa-pause"></i>
	{:else}
		<i class="fas fa-play"></i>
	{/if}
</button>


{@render children()}

