import { apiFetch } from "@/lib/apiClient";
import { env } from "@/lib/env";

export type Donation = {
  id: number;
  campaign_id: number;
  program_title?: string | null;
  amount: number;
  status: string;
  payment_url?: string;
  ref?: string;
  receipt_no?: string | null;
  channel?: string | null;
  created_at: string;
};

export async function createDonation(input: {
  campaign_id: number;
  amount: number;
  donor_note?: string;
  payment_channel?: 'transfer' | 'qris' | 'ewallet';
  provider?: string;
  base_amount?: number;
  client_unique_suffix?: number;
  use_unique_suffix?: boolean;
}) {
  if (env.useMock || !env.apiBaseUrl) {
    return {
      id: Math.floor(Math.random() * 10000),
      campaign_id: input.campaign_id,
      amount: input.amount,
      status: "pending",
      payment_url: "/mock-payment",
      created_at: new Date().toISOString(),
    } as Donation;
  }
  return apiFetch<Donation>(`/donations`, {
    method: "POST",
    body: JSON.stringify(input),
  });
}

export async function getDonationStatus(ref: string) {
  return apiFetch<{ ref: string; status: string; amount: number; campaign_id: number; channel: string; provider?: string; banks: Record<string,{account:string;name?:string}>; ewallets: Record<string,{number:string;name?:string}>; qris_url?:string }>(`/donations/status?ref=${encodeURIComponent(ref)}`);
}

export async function myDonations(page = 1) {
  if (env.useMock || !env.apiBaseUrl) {
    return {
      data: [
        {
          id: 10,
          campaign_id: 1,
          program_title: 'Bantu Pendidikan Anak',
          amount: 100000,
          status: "paid",
          created_at: new Date().toISOString(),
        },
      ] as Donation[],
      meta: { page, last_page: 1 },
    };
  }
  return apiFetch<{ data: Donation[]; meta?: any }>(`/donations/me?page=${page}`);
}

export async function getRecentDonations() {
  if (env.useMock || !env.apiBaseUrl) {
    return { data: [ { id:1, donor_name:'Ahmad S.', amount: 50000, created_at: new Date().toISOString(), program_title: 'Bantu Pendidikan' } ] };
  }
  return apiFetch<{ data: Array<{ id:number; donor_name:string; amount:number; created_at:string; program_title?:string }> }>(`/donations/recent`);
}
