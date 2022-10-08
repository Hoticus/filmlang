import React, { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import Spinner from "../components/Spinner"

const FilmDetail = ({ configuration }) => {
  const [isLoading, setIsLoading] = useState(true)
  const [details, setDetails] = useState(null)

  const { id } = useParams()

  useEffect(() => {
    const fetchData = async () => {
      setDetails(
        (await (await fetch("/api/get-movie-details/" + id)).json()).response,
      )

      setIsLoading(false)
    }

    fetchData()
  }, [])

  return isLoading ? (
    <div className="text-center mt-12 mb-4">
      <Spinner className="w-10 h-10" />
    </div>
  ) : (
    <div className="mt-12 px-6">
      <div className="container mx-auto flex gap-x-16">
        <div className="flex flex-col gap-y-2">
          <img
            className="rounded-lg shadow-md max-w-[14rem]"
            src={
              configuration.images.secure_base_url +
              (configuration.images.poster_sizes.includes("w500")
                ? "w500"
                : "original") +
              details.poster_path
            }
            alt=""
          />
        </div>
        <div className="w-full">
          <h1 className="text-2xl">
            {details.title}{" "}
            <span className="text-lg text-gray-600">
              ({details.release_date.substr(0, 4)})
            </span>
            <div
              className={
                "inline-block text-base ml-2 px-2 rounded-lg text-white " +
                (details.vote_average >= 7
                  ? "bg-green-500"
                  : details.vote_average < 5
                  ? "bg-red-500"
                  : "bg-gray-500")
              }
            >
              {details.vote_average.toFixed(1)}
            </div>
            <div
              className={
                "inline-block text-base ml-2 px-2 rounded-lg text-white " +
                {
                  1: "bg-green-400",
                  2: "bg-green-500",
                  3: "bg-yellow-400",
                  4: "bg-yellow-500",
                  5: "bg-orange-500",
                  6: "bg-red-500",
                }[details.language]
              }
            >
              {
                {
                  1: "A1",
                  2: "A2",
                  3: "B1",
                  4: "B2",
                  5: "C1",
                  6: "C2",
                }[details.language]
              }
            </div>
          </h1>
          <table className="table-auto w-full my-4">
            <tbody>
              <tr className="border-y">
                <td className="py-4 px-6 font-medium whitespace-nowrap">
                  Genres
                </td>
                <td className="py-4 px-6">
                  {details.genres.map((genre) => genre.name).join(", ")}
                </td>
              </tr>
              {!!details.production_countries.length && (
                <tr className="border-b">
                  <td className="py-4 px-6 font-medium whitespace-nowrap">
                    Countries
                  </td>
                  <td className="py-4 px-6">
                    {details.production_countries
                      .map((country) => country.name)
                      .join(", ")}
                  </td>
                </tr>
              )}
              {!!details.runtime && (
                <tr className="border-b">
                  <td className="py-4 px-6 font-medium whitespace-nowrap">
                    Runtime
                  </td>
                  <td className="py-4 px-6">
                    {Math.floor(details.runtime / 60)}h{" "}
                    {details.runtime - Math.floor(details.runtime / 60) * 60}m
                  </td>
                </tr>
              )}
              {!!details.production_companies.length && (
                <tr className="border-b">
                  <td className="py-4 px-6 font-medium whitespace-nowrap">
                    Production companies
                  </td>
                  <td className="py-4 px-6">
                    {details.production_companies
                      .map((company) => company.name)
                      .join(", ")}
                  </td>
                </tr>
              )}
            </tbody>
          </table>
          <p>{details.overview}</p>
        </div>
      </div>
    </div>
  )
}

export default FilmDetail
