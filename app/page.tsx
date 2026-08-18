import { Metadata } from 'next';
import { getCategorizedPrices } from '@/lib/data';
import { PriceListView } from '@/components/PriceListView';

export const metadata: Metadata = {
  title: 'DailyGurus Price List - Daily Wholesale Price List for Vegetables & Fruits',
  description: 'Check today\'s verified wholesale market prices for vegetables and fruits at Koyambedu Mandi, Chennai. Updated daily with transparent bulk rates.',
};

export default async function HomePage({
  searchParams,
}: {
  searchParams?: { date?: string };
}) {
  const requestedDate = searchParams?.date;
  const pricePayload = await getCategorizedPrices(requestedDate);

  return (
    <PriceListView
      initialData={pricePayload.data}
      initialDate={pricePayload.date}
      initialDateInfo={pricePayload.dateInfo}
      initialStats={pricePayload.stats}
      isHistorical={pricePayload.isHistorical}
    />
  );
}
