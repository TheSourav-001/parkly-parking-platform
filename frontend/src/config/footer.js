import { ref } from 'vue'

export const footerConfig = {
  brand: {
    name: 'Parkly',
    tagline: 'Find, compare and book parking in Dhaka.',
    description: 'Dhaka-first parking discovery and booking. Reported availability, server-calculated fees, digital invoices.',
    ctaText: 'Find Parking',
    ctaTarget: '/#find-parking',
  },
  disclaimer: {
    text: 'Availability is reported by operators and may not be real-time. Staff verify the invoice upon entry.',
    sandboxText: 'Payments are in sandbox mode during the prototype.',
    isSandbox: import.meta.env.VITE_PAYMENT_SANDBOX_MODE !== 'false',
  },
  contactInfo: {
    email: import.meta.env.VITE_CONTACT_EMAIL || 'support@parkly.com.bd',
    phone: import.meta.env.VITE_CONTACT_PHONE || '+880 1700-000000',
    hours: 'Sun – Thu, 9:00 AM – 6:00 PM BST',
  },
  socialLinks: [
    // Only rendered if URL is non-empty
    ...(import.meta.env.VITE_SOCIAL_LINKEDIN ? [{
      name: 'LinkedIn',
      url: import.meta.env.VITE_SOCIAL_LINKEDIN,
      icon: 'linkedin',
    }] : []),
    ...(import.meta.env.VITE_SOCIAL_TWITTER ? [{
      name: 'X (Twitter)',
      url: import.meta.env.VITE_SOCIAL_TWITTER,
      icon: 'twitter',
    }] : []),
    ...(import.meta.env.VITE_SOCIAL_FACEBOOK ? [{
      name: 'Facebook',
      url: import.meta.env.VITE_SOCIAL_FACEBOOK,
      icon: 'facebook',
    }] : []),
    ...(import.meta.env.VITE_SOCIAL_GITHUB ? [{
      name: 'GitHub',
      url: import.meta.env.VITE_SOCIAL_GITHUB,
      icon: 'github',
    }] : []),
  ],
  columns: [
    {
      id: 'platform',
      title: 'PLATFORM',
      ariaLabel: 'Platform navigation',
      links: [
        { label: 'Find Parking', href: '/#find-parking', isRoute: true },
        { label: 'How It Works', href: '/#journey', isRoute: true },
        { label: 'Vehicle Classes & Pricing', href: '/#pricing', isRoute: true },
        { label: 'Dhaka Coverage', href: '/#locations', isRoute: true },
      ],
    },
    {
      id: 'operators',
      title: 'FOR OPERATORS',
      ariaLabel: 'Operator services and partner links',
      links: [
        { label: 'List Your Parking', href: '/operators/enquiry', isRoute: true },
        { label: 'Operator & Admin Login', href: '/login', isRoute: true },
        { label: 'Operator FAQ', href: '/#faq', isRoute: true },
      ],
    },
    {
      id: 'support',
      title: 'SUPPORT',
      ariaLabel: 'Customer support and assistance',
      links: [
        { label: 'Help & FAQ', href: '/#faq', isRoute: true },
        { label: 'Contact Support', href: '/contact', isRoute: true },
        { label: 'Report a Facility Issue', href: '/contact?topic=report_discrepancy', isRoute: true },
      ],
    },
    {
      id: 'legal',
      title: 'LEGAL & TRUST',
      ariaLabel: 'Legal policies and compliance',
      links: [
        { label: 'Privacy Policy', href: '/privacy', isRoute: true },
        { label: 'Terms of Service', href: '/terms', isRoute: true },
        { label: 'Cancellation & Refund', href: '/cancellation-policy', isRoute: true },
        { label: 'Location & Data Use', href: '/location-and-data-use', isRoute: true },
      ],
    },
  ],
}
