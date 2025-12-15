interface ErrorDisplayProps {
  error: string | null;
}

export const ErrorDisplay = ({ error }: ErrorDisplayProps) => {
  if (!error) return null;

  return (
    <div className="mt-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg animate-fade-in">
      <p className="text-sm font-semibold text-red-800 mb-1">Error</p>
      <p className="text-red-600">{error}</p>
    </div>
  );
};
