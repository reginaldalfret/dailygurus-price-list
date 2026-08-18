/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  poweredByHeader: false,
  trailingSlash: false,
  async redirects() {
    return [
      {
        source: '/index.php',
        destination: '/',
        permanent: true,
      },
      {
        source: '/history.php',
        destination: '/history',
        permanent: true,
      },
      {
        source: '/about.php',
        destination: '/about',
        permanent: true,
      },
      {
        source: '/contact.php',
        destination: '/contact',
        permanent: true,
      },
      {
        source: '/admin/login.php',
        destination: '/admin',
        permanent: false,
      },
      {
        source: '/admin/login',
        destination: '/admin',
        permanent: false,
      },
      {
        source: '/admin/dashboard.php',
        destination: '/admin/dashboard',
        permanent: false,
      },
      {
        source: '/admin/prices.php',
        destination: '/admin/prices',
        permanent: false,
      },
      {
        source: '/admin/products.php',
        destination: '/admin/products',
        permanent: false,
      },
      {
        source: '/admin/history.php',
        destination: '/admin/history',
        permanent: false,
      },
    ];
  },
};

export default nextConfig;
