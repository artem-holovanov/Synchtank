<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const tracks = ref([])

function secondsToDuration(seconds) {
  const m = Math.floor(seconds / 60)
  const s = seconds % 60
  return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
}

async function fetchTracks() {
  try {
    const response = await axios.get('http://localhost:8000/api/tracks')
    tracks.value = response.data
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchTracks)
</script>

<template>
  <table>
    <thead>
    <tr>
      <th>Title</th>
      <th>Artist</th>
      <th>Duration</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="track in tracks" :key="track.id">
      <td>{{ track.title }}</td>
      <td>{{ track.artist }}</td>
      <td>{{ secondsToDuration(track.duration) }}</td>
    </tr>
    </tbody>
  </table>
</template>