interface ResultDisplayProps {
  result: number | null;
}

export const ResultDisplay = ({ result }: ResultDisplayProps) => {
  if (result === null) return null;

  return (
    <div className="mt-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg animate-fade-in">
      <p className="text-sm font-semibold text-green-800 mb-1">Result</p>
      <p className="text-3xl font-bold text-green-600">{result}</p>
    </div>
  );
};
