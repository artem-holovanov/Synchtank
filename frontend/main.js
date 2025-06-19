// frontend/main.js
import { createApp, ref, computed, onMounted } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios'
import { useTrackStore } from './stores/trackStore.js'

const App = {
    setup() {
        const store = useTrackStore()
        const editing = ref(null)

        const form = ref({
            title: '',
            artist: '',
            duration: '',
            isrc: ''
        })

        const errors = ref({})

        const secondsToDuration = (seconds) => {
            const mm = Math.floor(seconds / 60)
            const ss = String(seconds % 60).padStart(2, '0')
            return `${mm}:${ss}`
        }

        const parseDuration = (input) => {
            const match = input.trim().match(/^(\d+):(\d{2})$/)
            if (!match) return null
            const [, mm, ss] = match
            return parseInt(mm, 10) * 60 + parseInt(ss, 10)
        }

        const loadForm = (track) => {
            editing.value = track.id
            form.value = {
                title: track.title,
                artist: track.artist,
                duration: secondsToDuration(track.duration),
                isrc: track.isrc ?? ''
            }
        }

        const resetForm = () => {
            form.value = { title: '', artist: '', duration: '', isrc: '' }
            editing.value = null
            errors.value = {}
        }

        const submit = async () => {
            errors.value = {}

            const durationSeconds = parseDuration(form.value.duration)
            if (durationSeconds === null) {
                errors.value.duration = 'Invalid format (use mm:ss)'
                return
            }

            const payload = {
                title: form.value.title.trim(),
                artist: form.value.artist.trim(),
                duration: durationSeconds,
                isrc: form.value.isrc.trim() || null
            }

            if (!payload.title) errors.value.title = 'Required'
            if (!payload.artist) errors.value.artist = 'Required'
            if (payload.isrc && !/^[A-Z]{2}-[A-Z0-9]{3}-\\d{2}-\\d{5}$/.test(payload.isrc))
                errors.value.isrc = 'Invalid ISRC'

            if (Object.keys(errors.value).length > 0) return

            if (editing.value) {
                await store.updateTrack(editing.value, payload)
            } else {
                await store.createTrack(payload)
            }

            resetForm()
        }

        onMounted(() => {
            store.fetchTracks()
        })

        return {
            form,
            submit,
            resetForm,
            loadForm,
            errors,
            editing,
            store,
            secondsToDuration // ✅ expose this to the template
        }
    },
    template: `
      <h1>🎵 Track Manager</h1>

      <form @submit.prevent="submit">
        <label>
          Title:
          <input v-model="form.title" />
          <small v-if="errors.title" style="color:red">{{ errors.title }}</small>
        </label>
        <label>
          Artist:
          <input v-model="form.artist" />
          <small v-if="errors.artist" style="color:red">{{ errors.artist }}</small>
        </label>
        <label>
          Duration (mm:ss):
          <input v-model="form.duration" />
          <small v-if="errors.duration" style="color:red">{{ errors.duration }}</small>
        </label>
        <label>
          ISRC:
          <input v-model="form.isrc" />
          <small v-if="errors.isrc" style="color:red">{{ errors.isrc }}</small>
        </label>
        <button type="submit">{{ editing ? 'Update' : 'Create' }} Track</button>
        <button type="button" @click="resetForm" v-if="editing">Cancel</button>
      </form>

      <table v-if="store.tracks.length">
        <thead>
        <tr><th>Title</th><th>Artist</th><th>Duration</th><th>ISRC</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <tr v-for="track in store.tracks" :key="track.id">
          <td>{{ track.title }}</td>
          <td>{{ track.artist }}</td>
          <td>{{ secondsToDuration(track.duration) }}</td>
          <td>{{ track.isrc ?? '-' }}</td>
          <td><button @click="loadForm(track)">Edit</button></td>
        </tr>
        </tbody>
      </table>
    `
}

createApp(App).use(createPinia()).mount('#app')