<script setup>
/**
 * RoadmapSection Component
 * Transparently conveys "Planned, not available yet" future roadmap items
 * (ensuring no current fake claims about mobile apps, automated hardware, or wallet).
 */
import SectionHeader from '../common/SectionHeader.vue'

const roadmapItems = [
  {
    phase: 'Phase 2 (Planned)',
    title: 'More Operator Self-Service Tools',
    desc: 'Simplified mobile web attendant portal for one-tap slot updates and faster gate check-in verification.',
    status: 'In Design',
  },
  {
    phase: 'Phase 2 (Planned)',
    title: 'Dedicated Mobile Application',
    desc: 'Native iOS & Android Parkly app with offline invoice storage and instant push updates.',
    status: 'Planned',
  },
  {
    phase: 'Phase 3 (Planned)',
    title: 'Expanded Metropolitan Coverage',
    desc: 'Extending verified parking hub onboarding beyond Dhaka into Chittagong and Sylhet urban centers.',
    status: 'Planned',
  },
  {
    phase: 'Phase 3 (Planned)',
    title: 'Automated Direct Hardware Bridges',
    desc: 'Optional API plugins for commercial garages with existing ANPR or boom barrier hardware.',
    status: 'Research Phase',
  },
]
</script>

<template>
  <section id="roadmap" class="roadmap-section">
    <div class="roadmap-container">
      <SectionHeader
        eyebrow="Future Horizon"
        title="Product Roadmap (Planned Features)"
        subtitle="We clearly distinguish what works today from future research and planned engineering."
      />

      <div class="roadmap-grid">
        <div v-for="(item, idx) in roadmapItems" :key="idx" class="roadmap-card">
          <div class="card-meta">
            <span class="phase-tag">{{ item.phase }}</span>
            <span class="status-pill">{{ item.status }}</span>
          </div>
          <h3 class="roadmap-title">{{ item.title }}</h3>
          <p class="roadmap-desc">{{ item.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.roadmap-section {
  width: 100%;
  padding: 80px 24px;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-border);
}

.roadmap-container {
  max-width: 1200px;
  margin: 0 auto;
}

.roadmap-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 20px;
}

.roadmap-card {
  background: var(--color-bg);
  border: 1.5px dashed var(--color-border);
  border-radius: var(--radius-xl, 16px);
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.card-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.phase-tag {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-primary);
}

.status-pill {
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--color-text-muted);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  padding: 2px 8px;
  border-radius: 999px;
}

.roadmap-title {
  font-size: 1.0625rem;
  font-weight: 700;
  color: var(--color-text);
  margin: 0;
}

.roadmap-desc {
  font-size: 0.8125rem;
  color: var(--color-text-muted);
  line-height: 1.45;
  margin: 0;
}
</style>

