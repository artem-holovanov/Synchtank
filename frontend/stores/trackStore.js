import { defineStore } from 'pinia'
import axios from 'axios'

export const useTrackStore = defineStore('trackStore', {
    state: () => ({
        tracks: []
    }),
    actions: {
        async fetchTracks() {
            const { data } = await axios.get('http://localhost:8000/api/tracks')
            this.tracks = data
        },
        async createTrack(payload) {
            const { data } = await axios.post('http://localhost:8000/api/tracks', payload)
            this.tracks.push(data)
        },
        async updateTrack(id, payload) {
            const { data } = await axios.put(`http://localhost:8000/api/tracks/${id}`, payload)
            const i = this.tracks.findIndex(t => t.id === id)
            if (i !== -1) this.tracks[i] = data
        }
    }
})