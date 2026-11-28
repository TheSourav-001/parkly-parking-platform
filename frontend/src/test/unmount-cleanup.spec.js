import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import FaqSection from '../components/home/FaqSection.vue'
import VehicleDeck from '../components/home/VehicleDeck.vue'
import JourneyStory from '../components/home/JourneyStory.vue'
import RouteErrorBoundary from '../components/common/RouteErrorBoundary.vue'
import { defineComponent, h, nextTick } from 'vue'

describe('Component Lifecycle & Unmount Safety', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.spyOn(global, 'fetch').mockResolvedValue({
      ok: true,
      json: async () => ({ success: true, data: [] }),
    })
    // Mock matchMedia for happy-dom
    window.matchMedia = window.matchMedia || vi.fn().mockImplementation((query) => ({
      matches: false,
      media: query,
      onchange: null,
      addListener: vi.fn(),
      removeListener: vi.fn(),
      addEventListener: vi.fn(),
      removeEventListener: vi.fn(),
      dispatchEvent: vi.fn(),
    }))
  })

  describe('FaqSection unmount safety', () => {
    it('injects JSON-LD script on mount and removes it cleanly on unmount without throwing', () => {
      const removeEventListenerSpy = vi.spyOn(window, 'removeEventListener')

      const wrapper = mount(FaqSection, {
        global: {
          stubs: {
            'router-link': {
              template: '<a><slot /></a>',
            },
          },
        },
      })

      // Script should be injected into document.head
      const script = document.getElementById('parkly-faq-jsonld')
      expect(script).not.toBeNull()
      expect(script?.getAttribute('type')).toBe('application/ld+json')

      // Unmount must NOT throw NotFoundError
      expect(() => {
        wrapper.unmount()
      }).not.toThrow()

      // Script should be removed from head
      expect(document.getElementById('parkly-faq-jsonld')).toBeNull()
      expect(removeEventListenerSpy).toHaveBeenCalledWith('hashchange', expect.any(Function))
    })

    it('survives repeated mount and unmount cycles without error', () => {
      for (let i = 0; i < 3; i++) {
        const wrapper = mount(FaqSection, {
          global: {
            stubs: {
              'router-link': {
                template: '<a><slot /></a>',
              },
            },
          },
        })
        expect(() => wrapper.unmount()).not.toThrow()
      }
      expect(document.getElementById('parkly-faq-jsonld')).toBeNull()
    })
  })

  describe('VehicleDeck unmount and fallback safety', () => {
    it('unmounts cleanly and removes window load listener', () => {
      const removeEventListenerSpy = vi.spyOn(window, 'removeEventListener')

      const wrapper = mount(VehicleDeck, {
        global: {
          stubs: {
            SectionHeader: { template: '<div>SectionHeader</div>' },
            VehicleCard: { template: '<div>VehicleCard</div>' },
          },
        },
      })

      expect(() => {
        wrapper.unmount()
      }).not.toThrow()

      expect(removeEventListenerSpy).toHaveBeenCalledWith('load', expect.any(Function))
    })

    it('renders the 4 vehicle class cards and switcher pills', async () => {
      const wrapper = mount(VehicleDeck, {
        global: {
          stubs: {
            SectionHeader: { template: '<div>SectionHeader</div>' },
            VehicleCard: { template: '<div class="vehicle-card-stub">VehicleCard</div>' },
          },
        },
      })

      // Wait for async fetchRanges to settle
      await flushPromises()
      await nextTick()

      // The 4 vehicle classes must always be present
      expect(wrapper.text()).toContain('Car / Sedan')
      expect(wrapper.text()).toContain('SUV / 4x4')
      expect(wrapper.text()).toContain('Motorcycle')
      expect(wrapper.text()).toContain('Others / Light Commercial')
      expect(wrapper.findAll('.class-pill-btn')).toHaveLength(4)
      wrapper.unmount()
    })
  })

  describe('JourneyStory unmount safety', () => {
    it('unmounts without throwing error', () => {
      const wrapper = mount(JourneyStory, {
        global: {
          stubs: {
            SectionHeader: { template: '<div>SectionHeader</div>' },
          },
        },
      })

      expect(() => {
        wrapper.unmount()
      }).not.toThrow()
    })
  })

  describe('RouteErrorBoundary', () => {
    it('renders normal slot content when there are no errors', () => {
      const wrapper = mount(RouteErrorBoundary, {
        slots: {
          default: '<div class="healthy-content">Normal Page Content</div>',
        },
      })

      expect(wrapper.text()).toContain('Normal Page Content')
      expect(wrapper.find('.route-error-boundary').exists()).toBe(false)
    })

    it('captures child component error and displays navigation notice', async () => {
      const FaultyChild = defineComponent({
        setup() {
          throw new Error('Test crash in child component')
        },
        render() {
          return h('div', 'Broken')
        },
      })

      const wrapper = mount(RouteErrorBoundary, {
        slots: {
          default: h(FaultyChild),
        },
      })

      await nextTick()

      expect(wrapper.find('.route-error-boundary').exists()).toBe(true)
      expect(wrapper.text()).toContain('Something went wrong loading this section.')
      expect(wrapper.text()).toContain('Test crash in child component')
    })
  })
})
