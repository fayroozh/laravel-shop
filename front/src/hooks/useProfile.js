import { useQuery } from "@tanstack/react-query";
import authClient from "../services/auth-client";
import useAuthStore from "../app/authStore";


const useProfile = () => {
  const { token } = useAuthStore()
  const {
    data,
    isLoading,
    isError,
    error,
    refetch
  } = useQuery({
    queryKey: ["profile"],
    queryFn: () =>
      authClient.get("/auth/me").then((res) => res.data),
  });

  return {
    profile: data,
    isLoading,
    isError,
    error,
    refetchProfile: refetch
  };
};

export default useProfile;